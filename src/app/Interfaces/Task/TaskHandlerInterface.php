<?php

namespace App\Interfaces\Task;

use App\Models\Task\Task;
use App\Services\Dto\Task\TaskDto;

interface TaskHandlerInterface
{
    public function create(TaskDto $dto): Task;

    public function update(Task $task, TaskDto $dto): Task;
}
