<?php

namespace App\Casts\CastObject\Task\TaskHistoryExpander;

use App\Models\Task\Task;

class SnapshotHandler
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Получает все слепки задачи.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllSnapshots()
    {
        return $this->task->snapshots()->get()->groupBy('snapshot_date');
    }

    /**
     * Ищет последний доступный слепок до указанной даты.
     *
     * @param $snapshots
     * @param $currentDate
     * @return mixed|null
     */
    public function getLastSnapshotBeforeDate($snapshots, $currentDate)
    {
        $snapshotDates = array_keys($snapshots->toArray());
        rsort($snapshotDates);
        foreach ($snapshotDates as $snapshotDate) {
            if ($snapshotDate < $currentDate->format('Y-m-d')) {
                return $snapshots[$snapshotDate][0];
            }
        }

        return null;
    }
}
