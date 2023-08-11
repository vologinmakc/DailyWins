<?php

namespace App\Services\Task\Handler;

use App\Interfaces\Repository\TaskRepositoryInterface;
use App\Interfaces\Task\TaskHandlerInterface;
use App\Models\Task\Task;
use App\Services\Dto\Task\TaskDto;

class OneOffTaskHandler implements TaskHandlerInterface
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function create(TaskDto $dto): Task
    {
        return $this->taskRepository->create($dto);
    }

    public function update(Task $task, TaskDto $dto): Task
    {
        $task->update($dto->getAttribute());

        return $task;
    }
}
