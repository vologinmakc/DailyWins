<?php

namespace App\Casts\CastObject\Task\TaskHistoryExpander;

use App\Constants\Task\TaskStatuses;
use App\Models\Task\Task;
use App\Services\Dto\Task\TaskDto;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Класс TaskHistoryExpander
 *
 * Предоставляет расширенную историю задачи, формируя "снимок" задачи
 * на каждую активную дату, на которую она была запланирована. Данный снимок
 * включает в себя статус задачи и её подзадачи на конкретную дату.
 *
 * Методы:
 * @method 'getExpandedTasks': array Возвращает массив снимков задачи для каждой активной даты.
 */
class TaskHistoryExpander
{
    private $task;
    private $taskHistoryHandler;
    private $subTaskHandler;
    private $snapshotHandler;

    public function __construct(Task $task)
    {
        $this->task = $task;
        $this->taskHistoryHandler = new TaskHistoryHandler($task);
        $this->subTaskHandler = new SubTaskHandler($task);
        $this->snapshotHandler = new SnapshotHandler($task);
    }

    public function getExpandedTasks()
    {
        $tasks = [];
        /** @var TaskPeriod[] $periods */
        $periods = $this->taskHistoryHandler->generatePeriods();
        $subTasksWithStatus = $this->subTaskHandler->getSubTasksWithStatus();
        $snapshots = $this->snapshotHandler->getAllSnapshots();
        $subTaskStatuses = $this->subTaskHandler->getSubTaskStatuses($subTasksWithStatus->pluck('id')->toArray());

        foreach ($periods as $period) {
            $tasks = array_merge($tasks, $this->generateTasksForPeriod($period, $subTasksWithStatus, $snapshots, $subTaskStatuses));
        }

        return $tasks;
    }

    protected function generateTasksForPeriod(TaskPeriod $period, $subTasksWithStatus, $snapshots, $subTaskStatuses)
    {
        $tasks = [];
        $currentDate = clone $period->getStartDate();

        while ($currentDate <= $period->getEndedDate()) {
            if ($period->isActiveOn($currentDate)) {
                $status = $this->task->getTaskStatusForDate($currentDate->format('Y-m-d'));
                $taskDto = TaskDtoFactory::create($this->task, $period->getRecurrence(), $currentDate->format('Y-m-d'), $status);

                $snapshot = $snapshots[$currentDate->format('Y-m-d')][0] ?? $this->snapshotHandler->getLastSnapshotBeforeDate($snapshots, $currentDate);

                $taskDto->subtasks = $snapshot
                    ? array_map(function ($subTaskData) {
                        return $subTaskData;
                    }, $snapshot->subtasks_data)
                    : $this->subTaskHandler->formatSubTasks($subTasksWithStatus, $subTaskStatuses, $currentDate);

                $tasks[] = $taskDto->getAttribute();
            }

            $currentDate->addDay();
        }

        return $tasks;
    }
}

