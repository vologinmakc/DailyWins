<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/* Auth*/

Route::post('/login', [AuthController::class, 'login']);

Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
});
