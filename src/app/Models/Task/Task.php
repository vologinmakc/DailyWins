<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property SubTask $subtasks
 * @property int $created_by
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'created_by'];


    public function subtasks()
    {
        return $this->hasMany(SubTask::class);
    }

}
