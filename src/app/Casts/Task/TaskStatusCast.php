<?php

namespace App\Casts\Task;

use App\Casts\CastObject\Task\TaskStatusResolver;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TaskStatusCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return TaskStatusResolver::resolve($model);
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
