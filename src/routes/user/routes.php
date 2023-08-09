<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->middleware('auth')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
});
