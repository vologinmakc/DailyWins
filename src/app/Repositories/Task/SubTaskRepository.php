<?php

namespace App\Repositories\Task;

use App\Constants\Task\TaskType;
use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Models\Task\SubTask;
use App\Models\Task\SubTaskSnapshot;
use App\Models\Task\SubTaskStatus;
use App\Models\Task\Task;
use App\Services\Dto\Task\SubTaskDto;
use App\Services\Dto\Task\SubTaskStatusDto;
use Illuminate\Support\Facades\Auth;

class SubTaskRepository implements SubTaskRepositoryInterface
{
    public function create(SubTaskDto $subTaskDto)
    {
        return SubTask::create([
            'name'        => $subTaskDto->name,
            'description' => $subTaskDto->description,
            'status'      => $subTaskDto->status,
            'task_id'     => $subTaskDto->taskId,
            'created_by'  => Auth::id()
        ]);
    }

    public function update(SubTask $subTask, SubTaskDto $subTaskDto)
    {
        $subTask->update($subTaskDto->getAttribute());

        return $subTask;
    }

    public function updateStatus(SubTask $subTask, SubTaskStatusDto $dto)
    {
        SubTaskStatus::updateOrCreate(
            ['sub_task_id' => $subTask->id, 'date' => now()->format('Y-m-d')],
            $dto->getAttribute()
        );

        return $subTask;
    }

    public function delete(SubTask $subTask)
    {
        // Удаляем подзадачу
        return $subTask->delete();
    }

    public function find(int $id)
    {
        return SubTask::find($id);
    }

    public function createSnapshot(Task $task)
    {
        if ($task->type != TaskType::TYPE_RECURRING) {
            return; // Если нет, просто вернемся из функции и не будем создавать слепок
        }

        $subtasksData = [];
        /** @var SubTask $subtask */
        foreach ($task->subtasks as $subtask) {
            $subtasksData[] = [
                'id'          => $subtask->id,
                'name'        => $subtask->name,
                'description' => $subtask->description,
                'status'      => $subtask->status
            ];
        }

        // Удаляем существующий слепок для текущей даты, если он есть
        SubTaskSnapshot::where('task_id', $task->id)
            ->whereDate('snapshot_date', now()->format('Y-m-d'))
            ->delete();

        // Создаем новый слепок
        SubTaskSnapshot::create([
            'task_id'       => $task->id,
            'snapshot_date' => now(),
            'subtasks_data' => $subtasksData
        ]);
    }
}
