<?php

namespace App\Services\Dto\Task;

use App\Services\Dto\BaseDto;

class TaskDto extends BaseDto
{
    public string $name;
    public int    $status;
    public ?array $subtasks;
}
