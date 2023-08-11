<?php

namespace App\Casts\Task;

use App\Casts\CastObject\Task\TaskHistoryExpander;
use App\Models\Task\Task;

class TaskHistoryCast
{
    /**
     * @param Task $model
     * @param      $key
     * @param      $value
     * @param      $attributes
     * @return TaskHistoryExpander
     */
    public function get($model, $key, $value, $attributes)
    {
        return new TaskHistoryExpander($model);
    }

    public function set($model, $key, $value, $attributes)
    {
        return [];
    }
}
