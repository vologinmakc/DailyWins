<?php

namespace App\Http\Controllers\Task;

use App\Filters\FilterApplier;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Response\TaskResponse;
use App\Models\Task\SubTask;
use App\Models\Task\Task;
use App\Rules\RuleApplier;
use App\Services\Dto\Task\TaskDto;
use App\Services\Task\Handler\TaskHandlerFactory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends BaseController
{
    private TaskHandlerFactory $handlerFactory;

    public function __construct(TaskHandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    public function index(Request $request, FilterApplier $filterApplier, RuleApplier $ruleApplier)
    {
        Task::setExpandField(TaskResponse::expand());
        $query = $ruleApplier->applyRules(Task::query());
        $query = $filterApplier->applyFilters($query, $request);
        $tasks = $query->get();

        return $this->response($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        DB::beginTransaction();
        try {
            $dto = new TaskDto($request->validated());

            $handler = $this->handlerFactory->make($dto->type);
            $task = $handler->create($dto);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $this->response($task->load('subtasks'));
    }

    public function show(Task $task)
    {
        Task::setExpandField([
            'history' => fn(Task $task) => $task->history->getExpandedTasks()
        ]);

        if ($task->created_by == Auth::id()) {
            return $this->response($task->load('subtasks'));
        }

        throw new AuthorizationException('Данная задача не ваша');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($task->created_by != Auth::id()) {
            throw new AuthorizationException('Данная задача не ваша');
        }

        $dto = new TaskDto($request->validated());

        // Если тип задачи не предоставлен в запросе, используем текущий тип задачи из модели
        $taskType = $dto->type ?? $task->type;

        $handler = $this->handlerFactory->make($taskType);

        DB::beginTransaction();
        try {
            $updatedTask = $handler->update($task, $dto);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $this->response($updatedTask);
    }

    public function destroy(Task $task)
    {
        if ($task->created_by == Auth::id()) {
            DB::beginTransaction();
            try {
                $task->delete();
            } catch (\Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }

            DB::commit();

            return $this->response([]);
        }


        throw new AuthorizationException('Данная задача не ваша');
    }
}
