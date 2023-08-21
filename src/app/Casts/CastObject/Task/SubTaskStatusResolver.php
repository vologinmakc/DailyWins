<?php

namespace App\Casts\CastObject\Task;

use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\Task\SubTask;

class SubTaskStatusResolver
{
    /**
     * Посмотрим есть в запросе конкретная дата задачи если да то ищем статус для нее
     * @param SubTask $subTask
     * @return int
     */
    public static function resolve(SubTask $subTask): int
    {
        $date = ($subTask->task->type == TaskType::TYPE_RECURRING)
            ? request()->input('search.start_date_or_day') ?? now()->format('Y-m-d')
            : null;

        return $subTask->getSubTaskStatusForDate($date) ?? TaskStatuses::TASK_IN_PROGRESS;
    }

}
