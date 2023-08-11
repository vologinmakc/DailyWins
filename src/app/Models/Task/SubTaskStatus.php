<?php

namespace App\Models\Task;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int    $sub_task_id
 * @property int    $status
 * @property string $date
 */
class SubTaskStatus extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'sub_task_id',
        'status',
        'date'
    ];
}
