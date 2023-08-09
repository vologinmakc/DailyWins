<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    abstract public function apply(Builder $builder, $value): Builder;
}
