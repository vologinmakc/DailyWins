<?php

namespace App\Services\Task\Handler;

use App\Interfaces\Task\TaskHandlerInterface;
use App\Models\Task\Task;
use App\Repositories\Task\SubTaskRepository;
use App\Repositories\Task\TaskHistoryRepository;
use App\Repositories\Task\TaskRepository;
use App\Services\Dto\Task\SubTaskDto;
use App\Services\Dto\Task\TaskDto;

class RecurringTaskHandler implements TaskHandlerInterface
{
    private                       $taskRepository;
    private                       $subTaskRepository;
    private TaskHistoryRepository $historyRepository;

    public function __construct(
        TaskRepository        $taskRepository,
        SubTaskRepository     $subTaskRepository,
        TaskHistoryRepository $historyRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->subTaskRepository = $subTaskRepository;
        $this->historyRepository = $historyRepository;
    }

    public function create(TaskDto $dto): Task
    {
        // Создание основной задачи
        $task = $this->taskRepository->create($dto);

        // Создание каждой подзадачи, связанной с основной задачей
        foreach ($dto->subtasks as $subtaskData) {
            $this->subTaskRepository->create(
                new SubTaskDto($subtaskData + ['task_id' => $task->id])
            );
        }

        $this->historyRepository->create($task);

        return $task;
    }

    public function update(Task $task, TaskDto $dto): Task
    {
        // Логика обновления регулярной задачи
        $isUpdateRecurrence = $task->recurrence !== $dto->recurrence;
        $task->update($dto->getAttribute());

        if ($isUpdateRecurrence) {
            // Сохранение истории обновления регулярной задачи
            $this->historyRepository->create($task);
        }

        return $task;
    }
}
