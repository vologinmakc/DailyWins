<?php

namespace App\Models\Task;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatus extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'date',
        'task_id',
        'status',
        'commentary'
    ];
}
