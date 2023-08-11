<?php

namespace App\Casts\CastObject\Task;

class TaskPeriod
{
    public  $startDate;
    public  $endDate;
    public  $days;

    public function __construct($startDate, $endDate, $days)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->days = $days;
    }
}
