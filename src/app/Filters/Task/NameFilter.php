<?php

namespace App\Filters\Task;

use App\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class NameFilter extends Filter
{
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->where('name', $value);
    }
}
