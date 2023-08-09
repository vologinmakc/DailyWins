<?php

namespace App\Filters\Task;

use App\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DateFilter extends Filter
{
    public function apply(Builder $builder, $value): Builder
    {
        return $builder->whereDate('created_at', $value);
    }
}
