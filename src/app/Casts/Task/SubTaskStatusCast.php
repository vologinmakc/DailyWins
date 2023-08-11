<?php

namespace App\Casts\Task;

use App\Casts\CastObject\Task\SubTaskStatusResolver;
use App\Models\Task\SubTask;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SubTaskStatusCast implements CastsAttributes
{
    /**
     * @param SubTask $model
     * @param         $key
     * @param         $value
     * @param         $attributes
     * @return int|null
     */
    public function get($model, $key, $value, $attributes)
    {
        return SubTaskStatusResolver::resolve($model);
    }

    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}
