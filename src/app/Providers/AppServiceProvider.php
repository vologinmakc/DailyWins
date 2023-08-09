<?php

namespace App\Providers;

use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Interfaces\Repository\TaskRepositoryInterface;
use App\Repositories\Task\SubTaskRepository;
use App\Repositories\Task\TaskRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
