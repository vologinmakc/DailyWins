<?php

namespace App\Casts\CastObject\Task\TaskHistoryExpander;

use App\Models\Task\Task;
use App\Services\Dto\Task\TaskDto;

class TaskDtoFactory
{
    public static function create(Task $task, $recurrence, $date, $status)
    {
        $taskDto = new TaskDto($task->toArray());
        $taskDto->recurrence = $recurrence;
        $taskDto->date = $date;
        $taskDto->status = $status;

        return $taskDto;
    }
}
