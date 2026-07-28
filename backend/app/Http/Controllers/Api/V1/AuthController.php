<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Responses\AuthResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Infrastructure\Auth\JwtAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Cookie;

final class AuthController extends Controller
{
    private const COOKIE_NAME = 'aeroflash_token';
    private const COOKIE_MINUTES = 1440;

    public function __construct(private readonly JwtAuthService $jwtAuthService)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->jwtAuthService->attempt($request->input('email'), $request->input('password'));

        if (!$token) {
            Log::warning('Login failed: invalid credentials', ['email' => $request->input('email')]);

            return ApiResponse::error('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        Log::info('Login successful', ['email' => $request->input('email')]);

        $response = ApiResponse::success(
            AuthResponse::fromToken($token)->toArray()['data'],
            'Authentication successful'
        );

        return $response->withCookie($this->makeCookie($token));
    }

    public function refresh(Request $request): JsonResponse
    {
        $token = $request->cookie(self::COOKIE_NAME) ?? $request->bearerToken();

        if (!$token) {
            return ApiResponse::error('Token not provided', Response::HTTP_UNAUTHORIZED);
        }

        $newToken = $this->jwtAuthService->refreshToken($token);

        if (!$newToken) {
            Log::warning('Token refresh failed');

            return ApiResponse::error('Token is invalid or outside refresh window', Response::HTTP_UNAUTHORIZED)
                ->withoutCookie(self::COOKIE_NAME);
        }

        $response = ApiResponse::success(
            AuthResponse::fromToken($newToken)->toArray()['data'],
            'Token refreshed successfully'
        );

        return $response->withCookie($this->makeCookie($newToken));
    }

    public function logout(): JsonResponse
    {
        Log::info('User logged out', ['user_id' => auth()->user()?->id]);

        return ApiResponse::success(null, 'Logged out successfully')
            ->withoutCookie(self::COOKIE_NAME);
    }

    public function me(): JsonResponse
    {
        $user = auth()->user();

        return ApiResponse::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    private function makeCookie(string $token): Cookie
    {
        return cookie(
            name: self::COOKIE_NAME,
            value: $token,
            minutes: self::COOKIE_MINUTES,
            path: '/',
            domain: null,
            secure: false,
            httpOnly: true,
            raw: false,
            sameSite: 'Lax',
        );
    }
}
