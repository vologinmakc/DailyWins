<?php

namespace App\Services\Dto\Task;

use App\Services\Dto\BaseDto;

class SubTaskStatusDto extends BaseDto
{
    public int     $status;
    public ?string $commentary;

    public ?string $date;
}
