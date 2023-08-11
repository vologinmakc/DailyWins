<?php

namespace App\Repositories\Task;

use App\Interfaces\Repository\TaskHistoryRepositoryInterface;
use App\Models\Task\Task;
use App\Models\Task\TaskHistory;

class TaskHistoryRepository implements TaskHistoryRepositoryInterface
{
    public function create(Task $task): TaskHistory
    {
        $data = [
            'task_id'    => $task->id,
            'recurrence' => $task->recurrence,
            'changed_at' => now()
        ];

        return TaskHistory::create($data);
    }
}
