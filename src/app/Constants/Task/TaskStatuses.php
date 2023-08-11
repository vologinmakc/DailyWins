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
            self::TASK_NOT_STARTED => 'Not Started',
            self::TASK_IN_PROGRESS => 'In Progress',
            self::TASK_COMPLETED   => 'Completed',
        ];
    }

    public static function getStatusName(int $status): string
    {
        return self::getList()[$status] ?? 'Unknown';
    }
}
