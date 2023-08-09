<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $description
 * @property int    $status
 * @property int    $created_by
 * @property int    $task_id
 */
class SubTask extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'description', 'task_id', 'created_by'];
}
