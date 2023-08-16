<?php

namespace App\Providers;

use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Interfaces\Repository\TaskRepositoryInterface;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Models\Captcha\Captcha;
use App\Repositories\Task\SubTaskRepository;
use App\Repositories\Task\TaskRepository;
use App\Services\Captcha\CaptchaService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(SubTaskRepositoryInterface::class, SubTaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('custom_captcha', function ($attribute, $value, $parameters, $validator) {
            // $value - это значение капчи, введенное пользователем

            $token = request('captcha_token');

            // Если токена нет, возвращаем false
            if (!$token) {
                return false;
            }

            return (new CaptchaService())->validateCaptcha($token, $value);
        });
    }
}
