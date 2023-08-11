<?php

namespace App\Constants\Task;

use App\Constants\BaseConstant;

class TaskType extends BaseConstant
{
    const TYPE_ONE_OFF   = 1;
    const TYPE_RECURRING = 2;

    static function getList()
    {
        return [
            self::TYPE_ONE_OFF,
            self::TYPE_RECURRING
        ];
    }
}
