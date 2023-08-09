<?php

namespace App\Services\Task;

use App\Interfaces\Repository\TaskRepositoryInterface;
use App\Models\Task\Task;
use App\Services\Dto\Task\TaskDto;

class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function createTask(TaskDto $dto): Task
    {
        return $this->taskRepository->create($dto);
    }

    public function updateTask(Task $task, TaskDto $dto)
    {
        $this->taskRepository->update($task, $dto);

        return $task;
    }
}
