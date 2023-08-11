<?php

namespace App\Models\Task;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int    $task_id
 * @property string $snapshot_date
 * @property string subtasks_data
 */
class SubTaskSnapshot extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'snapshot_date',
        'subtasks_data'
    ];

    protected $casts = [
        'subtasks_data' => 'array'
    ];
}
