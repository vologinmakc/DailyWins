<?php

namespace App\Casts\CastObject\Task;

use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\Task\SubTask;

class SubTaskStatusResolver
{
    public static function resolve(SubTask $subTask): int
    {
        $date = ($subTask->task->type == TaskType::TYPE_RECURRING)
            ? now()->format('Y-m-d')
            : null;

        return $subTask->getSubTaskStatusForDate($date) ?? TaskStatuses::TASK_IN_PROGRESS;
    }

}
