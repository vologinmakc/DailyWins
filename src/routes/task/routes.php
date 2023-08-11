<?php

use App\Http\Controllers\Task\SubTaskController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('tasks')->middleware('auth')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::get('/{task}', [TaskController::class, 'show']);
    Route::get('/{task}/edit', [TaskController::class, 'edit']);
    Route::put('/{task}', [TaskController::class, 'update']);
    Route::delete('/{task}', [TaskController::class, 'destroy']);
});

Route::prefix('subtasks')->middleware('auth')->group(function () {
    Route::post('/', [SubTaskController::class, 'store']);
    Route::get('/{subTask}', [SubTaskController::class, 'show']);
    Route::put('/{subTask}', [SubTaskController::class, 'update']);
    Route::delete('/{subTask}', [SubTaskController::class, 'destroy']);
    Route::post('/{subTask}/status', [SubTaskController::class, 'updateStatus']);
});
