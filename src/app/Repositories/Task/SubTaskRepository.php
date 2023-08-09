<?php

namespace App\Repositories\Task;

use App\Interfaces\Repository\SubTaskRepositoryInterface;
use App\Models\Task\SubTask;
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
}
