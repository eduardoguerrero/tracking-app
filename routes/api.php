<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PackageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');

    Route::post('/auth/refresh', [AuthController::class, 'refresh'])
        ->middleware('throttle:login');

    Route::middleware('jwt-auth')->group(function () {
        Route::post('/packages', [PackageController::class, 'store']);
        Route::get('/packages/{tracking_number}', [PackageController::class, 'show']);
        Route::patch('/packages/{tracking_number}/status', [PackageController::class, 'updateStatus']);
    });
});
