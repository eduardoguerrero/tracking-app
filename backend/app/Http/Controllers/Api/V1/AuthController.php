<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Responses\AuthResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Infrastructure\Auth\JwtAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AuthController extends Controller
{
    public function __construct(private readonly JwtAuthService $jwtAuthService)
    {
    }

    public function login(LoginRequest $request)
    {
        $token = $this->jwtAuthService->attempt($request->input('email'), $request->input('password'));

        if (!$token) {
            return ApiResponse::error('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        return ApiResponse::success(AuthResponse::fromToken($token)->toArray()['data'], 'Authentication successful');
    }

    public function refresh(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::error('Token not provided', Response::HTTP_UNAUTHORIZED);
        }

        $newToken = $this->jwtAuthService->refreshToken($token);

        if (!$newToken) {
            return ApiResponse::error('Token is invalid or outside refresh window', Response::HTTP_UNAUTHORIZED);
        }

        return ApiResponse::success(AuthResponse::fromToken($newToken)->toArray()['data'], 'Token refreshed successfully');
    }
}
