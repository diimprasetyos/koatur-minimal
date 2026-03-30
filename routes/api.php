<?php

use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthApiController::class, 'me']);
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::post('/auth/switch-tenant', [AuthApiController::class, 'switchTenant']);
    });
});