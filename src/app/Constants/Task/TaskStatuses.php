<?php

namespace App\Constants\Task;

use App\Constants\BaseConstant;

class TaskStatuses extends BaseConstant
{
    const TASK_NOT_STARTED = 0;
    const TASK_IN_PROGRESS = 1;
    const TASK_COMPLETED   = 2;

    public static function getList(): array
    {
        return [
            self::TASK_NOT_STARTED,
            self::TASK_IN_PROGRESS,
            self::TASK_COMPLETED
        ];
    }

    public static function getStatusName(int $status): string
    {
        return self::getList()[$status] ?? 'Unknown';
    }
}
