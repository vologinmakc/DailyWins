<?php

namespace App\Filters\Task;

use App\Constants\Other\WeekDays;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class StartDateOrDayFilter
{
    public function apply(Builder $builder, $value): Builder
    {
        // Если значение представляет собой дату в формате 'YYYY-MM-DD'
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $dayOfWeek = Carbon::parse($value)->dayOfWeekIso;
            return $builder->where(function ($query) use ($value, $dayOfWeek) {
                $query->whereDate('start_date', $value)
                    ->orWhereJsonContains('recurrence', $dayOfWeek);
            });
        }

        // Если значение представляет собой день недели (например, 1 понедельник)
        if ($value !== null) {
            return $builder->whereJsonContains('recurrence', (int) $value);
        }

        return $builder;
    }
}
