<?php

namespace App\Http\Response;

use App\Models\Task\Task;

class TaskResponse
{
    public static function expand()
    {
        return [
            'user'      => function (Task $task) {
                return $task->author;
            },
            'sub_tasks' => function (Task $task) {
                return $task->subtasks;
            }
        ];
    }
}
