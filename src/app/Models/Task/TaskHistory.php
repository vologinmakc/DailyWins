<?php

namespace App\Models\Task;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskHistory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'recurrence',
        'comment',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'date'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getRecurrenceAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setRecurrenceAttribute($value)
    {
        $this->attributes['recurrence'] = is_array($value) ? json_encode($value) : $value;
    }

}
