<?php

namespace App\Repositories\Task;

use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Interfaces\Repository\TaskRepositoryInterface;
use App\Models\Task\Task;
use App\Services\Dto\Task\SubTaskDto;
use App\Services\Dto\Task\TaskDto;
use Illuminate\Support\Facades\Auth;

class TaskRepository implements TaskRepositoryInterface
{
    private SubTaskRepositoryInterface $subTaskRepository;

    public function __construct(SubTaskRepositoryInterface $subTaskRepository)
    {
        $this->subTaskRepository = $subTaskRepository;
    }

    public function create(TaskDto $taskDto)
    {
        $task = Task::create([
            'name'       => $taskDto->name,
            'status'     => $taskDto->status,
            'created_by' => Auth::id()
        ]);

        foreach ($taskDto->subtasks as $subtaskData) {
            $this->subTaskRepository->create(new SubTaskDto($subtaskData + ['task_id' => $task->id]));
        }

        return $task;
    }

    public function update(\App\Models\Task\Task $task, TaskDto $dto)
    {
        return $task->update($dto->getAttribute());
    }
}
