<?php

namespace App\Models\Task;

use App\Casts\CastObject\Task\TaskHistoryExpander\TaskHistoryExpander;
use App\Casts\Task\TaskHistoryCast;
use App\Casts\Task\TaskStatusCast;
use App\Constants\Task\TaskStatuses;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property SubTask                  $subtasks
 * @property int                      $created_by
 * @property User                     $author
 * @property TaskHistoryExpander      $history
 * @property TaskHistory[]|Collection $taskHistory
 */
class Task extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'created_by', 'start_date', 'description', 'type', 'recurrence'];
    protected $appends  = ['status'];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'recurrence' => 'array',
        'history'    => TaskHistoryCast::class,
        'status'     => TaskStatusCast::class
    ];

    public function taskHistory()
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function getTaskStatusForDate($date = null)
    {
        $query = $this->taskStatus();
        if ($date) {
            $query->where('date', $date);
        }

        return ($status = $query->first()) ? $status->status : TaskStatuses::TASK_IN_PROGRESS;
    }

    public function taskStatus()
    {
        return $this->hasOne(TaskStatus::class);
    }

    public function subtasks()
    {
        return $this->hasMany(SubTask::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function snapshots()
    {
        return $this->hasMany(SubTaskSnapshot::class);
    }

}
