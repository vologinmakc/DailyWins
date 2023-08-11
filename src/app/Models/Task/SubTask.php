<?php

namespace App\Models\Task;


use App\Constants\Task\TaskStatuses;
use App\Constants\Task\TaskType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $description
 * @property int    $status
 * @property int    $created_by
 * @property int    $task_id
 */
class SubTask extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'task_id', 'created_by'];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        // Получаем тип основной задачи
        // Если задача периодическая
        if ($this->task->type == TaskType::TYPE_RECURRING) {
            $currentDate = date('Y-m-d'); // Получаем текущую дату

            return $this->getSubTaskStatusForDate($currentDate) ?: null;
        }

        // Для всех других сценариев, возвращаем статус, как прежде
        $status = $this->getSubTaskStatusForDate();
        return $status ? $status->status : TaskStatuses::TASK_IN_PROGRESS;
    }

    private function getSubTaskStatusForDate($date = null)
    {
        $query = $this->hasOne(SubTaskStatus::class);
        if ($date) {
            $query->where('date', $date);
        }
        return $query->first();
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
