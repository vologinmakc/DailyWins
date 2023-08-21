<?php

namespace App\Services\Dto\Task;

use App\Services\Dto\BaseDto;

class SubTaskDto extends BaseDto
{
    public ?string $name;
    public ?string $description;
    public ?int $taskId;

}
