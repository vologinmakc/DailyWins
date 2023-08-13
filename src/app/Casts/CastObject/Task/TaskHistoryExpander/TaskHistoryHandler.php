<?php

namespace App\Casts\CastObject\Task\TaskHistoryExpander;

use App\Models\Task\Task;
use Illuminate\Support\Carbon;

class TaskHistoryHandler
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Создает периоды на основе истории задачи.
     *
     * @return array
     */
    public function generatePeriods()
    {
        $periods = [];

        $histories = $this->task->taskHistory->sortBy('changed_at');
        foreach ($histories as $index => $history) {
            $startDate = $history->changed_at;
            // Начало следующего переода или сегодня
            $endDate = isset($histories[$index + 1]) ? $histories[$index + 1]->changed_at : Carbon::now();
            $periods[] = new TaskPeriod($startDate, $endDate, $history['recurrence']);
        }

        return $periods;
    }
}
