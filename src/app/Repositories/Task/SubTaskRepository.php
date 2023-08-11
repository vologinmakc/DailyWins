<?php

namespace App\Repositories\Task;

use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Models\Task\SubTask;
use App\Models\Task\SubTaskSnapshot;
use App\Models\Task\Task;
use App\Models\Task\TaskHistory;
use App\Services\Dto\Task\SubTaskDto;
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

    public function delete(SubTask $subTask)
    {
        return $subTask->delete();
    }

    public function find(int $id)
    {
        return SubTask::find($id);
    }

    public function createSnapshot(Task $task)
    {
        $subtasks = $task->subtasks; // Получаем все подзадачи задачи
        $subtasksData = [];
        /** @var SubTask $subtask */
        foreach ($subtasks as $subtask) {
            $subtasksData[] = [
                'id'          => $subtask->id,
                'name'        => $subtask->name,
                'description' => $subtask->description,
                'status'      => $subtask->status
            ];
        }

        // Создаем запись в таблице subtask_snapshots
        SubTaskSnapshot::create([
            'task_id'       => $task->id,
            'snapshot_date' => now(),
            'subtasks_data' => $subtasksData
        ]);
    }

}
