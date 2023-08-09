<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class FilterApplier
 *
 * Этот класс предназначен для динамического применения фильтров к запросу Eloquent Builder на основе
 * входящего HTTP-запроса. Фильтры определяются динамически на основе имени модели и переданных параметров
 * фильтрации в HTTP-запросе.
 *
 * Пример использования:
 * 1. Пользователь отправляет запрос с параметром фильтрации `search[name]=John`.
 * 2. FilterApplier ищет соответствующий класс фильтра для модели на основе имени параметра.
 * 3. Если класс фильтра существует, он применяется к запросу Builder.
 * 4. Также поддерживается сортировка с помощью параметра `sort`. Например, `sort=-id` сортирует по убыванию.
 *
 */
class FilterApplier
{
    public function applyFilters(Builder $query, Request $request): Builder
    {
        $modelClass = get_class($query->getModel());
        $filters = $request->input('search', []);

        foreach ($filters as $filterName => $value) {
            $filterClass = 'App\\Filters\\' . class_basename($modelClass) . '\\' . ucfirst($filterName) . 'Filter';
            if (class_exists($filterClass)) {
                $filter = new $filterClass;
                $query = $filter->apply($query, $value);
            }
        }

        // Применение сортировки
        $sort = $request->input('sort');
        if ($sort) {
            $direction = Str::startsWith($sort, '-') ? 'desc' : 'asc';
            $column = ltrim($sort, '-');
            $query = $query->orderBy($column, $direction);
        }

        return $query;
    }
}
