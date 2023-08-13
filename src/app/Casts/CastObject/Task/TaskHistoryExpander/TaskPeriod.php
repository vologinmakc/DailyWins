<?php

namespace App\Casts\CastObject\Task\TaskHistoryExpander;

use Illuminate\Support\Carbon;

class TaskPeriod
{
    private        $startDate;
    private        $recurrence;
    private Carbon $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate, array $recurrence)
    {
        $this->startDate = $startDate;
        $this->recurrence = $recurrence;
        $this->endDate = $endDate;
    }

    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    public function getEndedDate(): Carbon
    {
        return $this->endDate;
    }

    public function isActiveOn(Carbon $date): bool
    {
        return in_array($date->dayOfWeek, $this->recurrence);
    }

    /**
     * @return array
     */
    public function getRecurrence(): array
    {
        return $this->recurrence;
    }
}
