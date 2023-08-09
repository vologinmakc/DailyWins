<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreSubTaskRequest;
use App\Http\Requests\Task\UpdateSubTaskRequest;
use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Models\Task\SubTask;
use App\Services\Dto\Task\SubTaskDto;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubTaskController extends Controller
{
    protected $subTaskRepository;

    public function __construct(SubTaskRepositoryInterface $subTaskRepository)
    {
        $this->subTaskRepository = $subTaskRepository;
    }

    public function store(StoreSubTaskRequest $request)
    {
        DB::beginTransaction();
        try {
            $dto = new SubTaskDto($request->validated());
            $subTask = $this->subTaskRepository->create($dto);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $subTask;
    }

    public function show(SubTask $subTask)
    {
        if ($subTask->created_by == Auth::id()) {
            return $subTask;
        }

        throw new AuthorizationException('Данная задача не ваша');
    }

    public function update(UpdateSubTaskRequest $request, SubTask $subTask)
    {
        if ($subTask->created_by == Auth::id()) {
            DB::beginTransaction();
            try {
                $dto = new SubTaskDto($request->validated());
                $subTask = $this->subTaskRepository->update($subTask, $dto);
            } catch (\Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }

            DB::commit();

            return $subTask;
        }

        throw new AuthorizationException('Данная задача не ваша');

    }

    public function destroy(SubTask $subTask)
    {
        if ($subTask->created_by == Auth::id()) {
            DB::beginTransaction();
            try {
                $subTask->delete();
            } catch (\Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }
            DB::commit();

            return [];
        }

        throw new AuthorizationException('Данная задача не ваша');

    }
}
