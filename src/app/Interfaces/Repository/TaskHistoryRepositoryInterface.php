<?php

namespace App\Interfaces\Repository;

use App\Models\Task\Task;
use App\Models\Task\TaskHistory;

interface TaskHistoryRepositoryInterface
{
    public function create(Task $task): TaskHistory;
}
