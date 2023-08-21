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
            'name'        => $taskDto->name,
            'description' => $taskDto->description,
            'start_date'  => $taskDto->startDate,
            'end_date'  => $taskDto->endDate,
            'type'        => $taskDto->type,
            'recurrence'  => $taskDto->recurrence,
            'created_by'  => Auth::id()
        ]);

        return $task;
    }

    public function update(\App\Models\Task\Task $task, TaskDto $dto)
    {
        return $task->update($dto->getAttribute());
    }
}
