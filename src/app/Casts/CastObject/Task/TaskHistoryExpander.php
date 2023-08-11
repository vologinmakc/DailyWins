<?php

namespace App\Casts\CastObject\Task;

use App\Models\Task\SubTaskSnapshot;
use App\Models\Task\Task;
use App\Services\Dto\Task\TaskDto;
use Illuminate\Support\Carbon;

/**
 * Класс TaskHistoryExpander
 *
 * Предоставляет расширенную историю задачи, формируя "снимок" задачи
 * на каждую активную дату, на которую она была запланирована. Данный снимок
 * включает в себя статус задачи и её подзадачи на конкретную дату.
 *
 * Методы:
 * @method 'getExpandedTasks': array Возвращает массив снимков задачи для каждой активной даты.
 */
class TaskHistoryExpander
{
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Возвращает историю задачи, учитывая её повторяемость и слепки подзадач.
     * Для каждой даты активности задачи использует последний доступный слепок подзадач.
     * То есть если пользователь поменял подзадачи в задаче то будет использоваться слепок
     *
     * @return array
     */
    public function getExpandedTasks()
    {
        $tasks = [];
        $periods = [];

        // Создаем периоды на основе истории задачи
        $histories = $this->task->taskHistory->sortBy('changed_at');
        foreach ($histories as $index => $history) {
            $startDate = $history->changed_at;
            $endDate = isset($histories[$index + 1]) ? $histories[$index + 1]->changed_at : Carbon::now();
            $periods[] = new TaskPeriod($startDate, $endDate, $history['recurrence']);
        }

        // Генерируем задачи на основе периодов
        foreach ($periods as $period) {
            $currentDate = clone $period->startDate;

            while ($currentDate <= $period->endDate) {
                if (in_array($currentDate->dayOfWeek, $period->days)) {
                    $subTaskSnapshot = SubTaskSnapshot::where('task_id', $this->task->id)
                        ->where('snapshot_date', '<=', $currentDate)
                        ->latest('snapshot_date')
                        ->first();

                    $taskDto = new TaskDto($this->task->toArray());
                    $taskDto->recurrence = $period->days;
                    $taskDto->date = $currentDate->format('Y-m-d');
                    $taskDto->status = $this->task->getTaskStatusForDate($currentDate->format('Y-m-d'));

                    if ($subTaskSnapshot) {
                        $taskDto->subtasks = $subTaskSnapshot->subtasks_data;
                    } else {
                        $taskDto->subtasks = $this->task->subtasks;
                    }

                    foreach ($taskDto->subtasks as $subtask) {
                        $subtask->status = $subtask->getSubTaskStatusForDate($currentDate->format('Y-m-d'));
                    }

                    $tasks[] = $taskDto->getAttribute();
                }

                $currentDate->addDay();
            }
        }

        return $tasks;
    }


}
