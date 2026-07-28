<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Infrastructure\Auth\JwtAuthService;
use Closure;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Response;

class JwtAuthenticate
{
    public function __construct(private readonly JwtAuthService $jwtAuthService,)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return ApiResponse::error('Token not provided', Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->jwtAuthService->getUserFromToken($token);
        if (!$user) {
            return ApiResponse::error('Invalid or expired token', Response::HTTP_UNAUTHORIZED);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
