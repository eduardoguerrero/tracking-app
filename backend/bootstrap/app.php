<?php

use App\Domain\Exceptions\InvalidStatusTransitionException;
use App\Domain\Exceptions\PackageNotFoundException;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt-auth' => \App\Http\Middleware\JwtAuthenticate::class,
            'security-headers' => \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (PackageNotFoundException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        });

        $exceptions->render(function (InvalidStatusTransitionException $e) {
            return ApiResponse::error($e->getMessage(), 400);
        });

        $exceptions->render(function (InvalidArgumentException $e, Request $request) {
            if ($request->is('api/*')) {
                $decoded = json_decode($e->getMessage(), true);
                return ApiResponse::error('Validation error', 400, $decoded);
            }

            return null;
        });

        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::error('Validation error', 400, $e->errors());
        });
    })->create();
