<?php


use Illuminate\Support\Facades\Route;

Route::prefix('captcha')->middleware('api')->group(function () {
    Route::get('/generate', [\App\Http\Controllers\Captcha\CaptchaController::class, 'generate']);
});
