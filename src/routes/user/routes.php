<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/* Auth*/

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/register', [UserController::class, 'register']);
});

// Public
Route::prefix('user')->middleware('api')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
});
