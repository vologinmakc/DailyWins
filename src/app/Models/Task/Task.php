<?php

namespace App\Models\Task;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property SubTask $subtasks
 * @property int     $created_by
 * @property User    $author
 */
class Task extends BaseModel
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'created_by'];


    public function subtasks()
    {
        return $this->hasMany(SubTask::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
