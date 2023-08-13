<?php

namespace App\Casts\CastObject\Task\TaskHistoryExpander;

use App\Constants\Task\TaskStatuses;
use App\Models\Task\SubTaskStatus;
use App\Models\Task\Task;
use Illuminate\Support\Arr;

class SubTaskHandler
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Получает все подзадачи с их статусами.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSubTasksWithStatus()
    {
        return $this->task->subtasks()->with(['subTaskStatus'])->get()->keyBy('id');
    }

    /**
     * Получает статусы подзадач.
     *
     * @param $subTaskIds
     * @return \Illuminate\Support\Collection
     */
    public function getSubTaskStatuses($subTaskIds)
    {
        return SubTaskStatus::query()
            ->whereIn('sub_task_id', $subTaskIds)
            ->get()
            ->groupBy('sub_task_id');
    }

    /**
     * Формирует данные подзадачи.
     *
     * @param $subTask
     * @param $subTaskStatuses
     * @param $currentDate
     * @return array
     */
    public function formatSubTaskData($subTask, $subTaskStatuses, $currentDate)
    {
        $statusRecord = Arr::get($subTaskStatuses, $subTask->id)
            ?->where('date', $currentDate->format('Y-m-d'))
            ->first();

        return [
            'id'          => $subTask->id,
            'name'        => $subTask->name,
            'description' => $subTask->description,
            'status'      => $statusRecord ? $statusRecord->status : TaskStatuses::TASK_IN_PROGRESS
        ];
    }

    public function formatSubTasks($subTasksWithStatus, $subTaskStatuses, $currentDate)
    {
        return $subTasksWithStatus->map(function ($subTask) use ($subTaskStatuses, $currentDate) {
            return $this->formatSubTaskData($subTask, $subTaskStatuses, $currentDate);
        })->toArray();
    }
}
