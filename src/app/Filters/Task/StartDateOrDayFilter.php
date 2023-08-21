<?php

namespace App\Filters\Task;

use App\Constants\Other\WeekDays;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class StartDateOrDayFilter
{
    public function apply(Builder $builder, $value): Builder
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $dayOfWeek = Carbon::parse($value)->dayOfWeekIso;
            return $builder->where(function ($query) use ($value, $dayOfWeek) {
                $query->whereDate('start_date', $value)
                    ->orWhereJsonContains('recurrence', $dayOfWeek);
            })->whereDate('created_at', '<=', $value) // для повторяющихся задач нельзя показать ее раньше даты ее создания
            ->where(function ($query) use ($value) {
                $query->whereDate('end_date', '>=', $value) // end_date должно быть сегодня или позже
                ->orWhereNull('end_date'); // или end_date может быть null
            });
        }

        // Если значение представляет собой день недели (например, 1 понедельник)
        if ($value !== null) {
            return $builder->whereJsonContains('recurrence', (int)$value);
        }

        return $builder;
    }
}
