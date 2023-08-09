<?php

namespace App\Http\Controllers\Task;

use App\Filters\FilterApplier;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task\Task;
use App\Rules\RuleApplier;
use App\Services\Dto\Task\TaskDto;
use App\Services\Task\TaskService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends BaseController
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request, FilterApplier $filterApplier, RuleApplier $ruleApplier)
    {
        // TODO сделать отдельно потом
        Task::setExpandField([
            'user' => function (Task $task) {
                return $task->author;
            }
        ]);
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
            $task = $this->taskService->createTask($dto);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $this->response($task->load('subtasks'));
    }

    public function show(Task $task)
    {
        if ($task->created_by == Auth::id()) {
            return $this->response($task->load('subtasks'));
        }

        throw new AuthorizationException('Данная задача не ваша');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($task->created_by == Auth::id()) {
            DB::beginTransaction();
            try {
                $task = $this->taskService->updateTask($task, new TaskDto($request->validationData()));
            } catch (\Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }
            DB::commit();

            return $this->response($task);
        }

        throw new AuthorizationException('Данная задача не ваша');
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
