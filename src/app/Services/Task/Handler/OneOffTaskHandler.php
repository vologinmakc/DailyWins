<?php

namespace App\Services\Task\Handler;

use App\Interfaces\Repository\TaskRepositoryInterface;
use App\Interfaces\Task\TaskHandlerInterface;
use App\Models\Task\Task;
use App\Repositories\Task\SubTaskRepository;
use App\Services\Dto\Task\SubTaskDto;
use App\Services\Dto\Task\TaskDto;

class OneOffTaskHandler implements TaskHandlerInterface
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository, SubTaskRepository $subTaskRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->subTaskRepository = $subTaskRepository;
    }

    public function create(TaskDto $dto): Task
    {
        $task = $this->taskRepository->create($dto);

        foreach ($dto->subtasks as $subtaskData) {
            $this->subTaskRepository->create(
                new SubTaskDto($subtaskData + ['task_id' => $task->id])
            );
        }

        return $task;
    }

    public function update(Task $task, TaskDto $dto): Task
    {
        $task->update($dto->getAttribute());

        return $task;
    }
}
