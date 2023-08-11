<?php

namespace App\Casts\CastObject\Task;

use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\Task\Task;

class TaskStatusResolver
{
    public static function resolve(Task $task): int
    {
        $date = ($task->type == TaskType::TYPE_RECURRING)
            ? now()->format('Y-m-d')
            : null;

        return $task->getTaskStatusForDate($date) ?? TaskStatuses::TASK_IN_PROGRESS;
    }
}
