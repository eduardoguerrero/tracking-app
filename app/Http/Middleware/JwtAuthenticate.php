<?php

namespace App\Http\Middleware;

use App\Infrastructure\Auth\JwtAuthService;
use Closure;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;

class JwtAuthenticate
{
    public function __construct(
        private readonly JwtAuthService $jwtAuthService,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return ApiResponse::error('Token not provided', 401);
        }

        $user = $this->jwtAuthService->getUserFromToken($token);

        if (!$user) {
            return ApiResponse::error('Invalid or expired token', 401);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
