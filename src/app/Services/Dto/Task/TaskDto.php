<?php

namespace App\Services\Dto\Task;

use App\Services\Dto\BaseDto;

class TaskDto extends BaseDto
{
    public ?string $name;
    public ?int    $status;
    public         $subtasks;
    public ?int    $type;
    public ?string $description;
    public ?string $startDate;
    public ?string $date;
    public         $recurrence;
}
