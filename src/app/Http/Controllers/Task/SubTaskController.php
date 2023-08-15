<?php

namespace App\Http\Controllers\Task;

use App\Constants\Task\TaskStatuses;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Task\StoreSubTaskRequest;
use App\Http\Requests\Task\UpdateSubTaskRequest;
use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Models\Task\SubTask;
use App\Services\Dto\Task\SubTaskDto;
use App\Services\Dto\Task\SubTaskStatusDto;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubTaskController extends BaseController
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
            $this->subTaskRepository->createSnapshot($subTask->task);
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $this->response($subTask);
    }

    public function show(SubTask $subTask)
    {
        if ($subTask->created_by == Auth::id()) {
            return $this->response($subTask);
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

            return $this->response($subTask);
        }

        throw new AuthorizationException('Данная задача не ваша');
    }

    public function updateStatus(Request $request, SubTask $subTask)
    {
        $statusDto = new SubTaskStatusDto($request->validate([
            'status'     => 'required|in:' . implode(',', TaskStatuses::getList()),
            'commentary' => 'nullable|string|255'
        ]));

        if ($subTask->created_by == Auth::id()) {
            DB::beginTransaction();
            try {
                $this->subTaskRepository->updateStatus($subTask, $statusDto);
                $this->subTaskRepository->createSnapshot($subTask->task);
            } catch (\Throwable $exception) {
                DB::rollBack();
                throw $exception;
            }
            DB::commit();

            return $this->response($subTask);
        }

        throw new AuthorizationException('Данная подзадача не ваша');
    }


    public function destroy(SubTask $subTask)
    {
        if ($subTask->created_by == Auth::id()) {
            DB::beginTransaction();
            try {
                $subTask->delete();
                $this->subTaskRepository->createSnapshot($subTask->task);
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
