<?php

namespace App\Casts\CastObject\Task;

use App\Constants\Task\TaskStatuses;
use App\Models\Task\Task;
use App\Services\Dto\Task\TaskDto;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Предварительная загрузка всех подзадач и их статусов
        $subTasksWithStatus = $this->task->subtasks()->with(['subTaskStatus'])->get()->keyBy('id');

        // Предварительная загрузка всех слепков
        $snapshots = $this->task->snapshots()->get()->groupBy('snapshot_date');

        // Предварительная загрузка всех статусов подзадач
        $subTaskStatuses = DB::table('sub_task_statuses')
            ->whereIn('sub_task_id', $subTasksWithStatus->pluck('id')->toArray())
            ->get()
            ->groupBy('sub_task_id');

        // Генерируем задачи на основе периодов
        foreach ($periods as $period) {
            $currentDate = clone $period->startDate;

            while ($currentDate <= $period->endDate) {
                if (in_array($currentDate->dayOfWeek, $period->days)) {
                    $taskDto = new TaskDto($this->task->toArray());
                    $taskDto->recurrence = $period->days;
                    $taskDto->date = $currentDate->format('Y-m-d');
                    $taskDto->status = $this->task->getTaskStatusForDate($currentDate->format('Y-m-d'));

                    $snapshot = $snapshots[$currentDate->format('Y-m-d')][0] ?? null;

                    if (!$snapshot) {
                        // Используем последний доступный слепок до этой даты
                        $snapshotDates = array_keys($snapshots->toArray());
                        rsort($snapshotDates);
                        foreach ($snapshotDates as $snapshotDate) {
                            if ($snapshotDate < $currentDate->format('Y-m-d')) {
                                $snapshot = $snapshots[$snapshotDate][0];
                                break;
                            }
                        }
                    }

                    if ($snapshot) {
                        $taskDto->subtasks = array_map(function ($subTaskData) {
                            return $subTaskData;
                        }, $snapshot->subtasks_data);
                    } else {
                        $taskDto->subtasks = $subTasksWithStatus->map(function ($subTask) use ($subTaskStatuses, $currentDate) {
                            $statusRecord = Arr::get($subTaskStatuses, $subTask->id)
                                ?->where('date', $currentDate->format('Y-m-d'))
                                ->first();

                            return [
                                'id'          => $subTask->id,
                                'name'        => $subTask->name,
                                'description' => $subTask->description,
                                'status'      => $statusRecord ? $statusRecord->status : TaskStatuses::TASK_IN_PROGRESS
                            ];
                        })->toArray();
                    }

                    $tasks[] = $taskDto->getAttribute();
                }

                $currentDate->addDay();
            }
        }

        return $tasks;
    }
}
