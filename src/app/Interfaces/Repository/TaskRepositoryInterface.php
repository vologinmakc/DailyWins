<?php

namespace App\Interfaces\Repository;

use App\Services\Dto\Task\TaskDto;

interface TaskRepositoryInterface
{
    public function create(TaskDto $taskDto);

    public function update(\App\Models\Task\Task $task, TaskDto $dto);
}
