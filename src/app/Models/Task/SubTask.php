<?php

namespace App\Models\Task;


use App\Casts\Task\SubTaskStatusCast;
use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string $description
 * @property int    $status
 * @property int    $created_by
 * @property int    $task_id
 */
class SubTask extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'task_id', 'created_by'];

    protected $appends = ['status'];

    protected $casts = [
        'status' => SubTaskStatusCast::class
    ];

    public function getSubTaskStatusForDate($date = null)
    {
        $statusRelation = $this->subTaskStatus();

        if ($date) {
            $statusRelation->where('date', $date);
        }

        return optional($statusRelation->first())->status;
    }

    public function subTaskStatus()
    {
        return $this->hasOne(SubTaskStatus::class);
    }


    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getSnapshotForDate($date)
    {
        return $this->snapshots()->where('date', $date)->first();
    }

    public function snapshots()
    {
        return $this->hasMany(SubTaskSnapshot::class);
    }
}
