<?php

namespace App\Http\Requests\Task;

use App\Constants\Other\WeekDays;
use App\Constants\Task\TaskType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function rules()
    {
        $task = $this->route('task'); // Получаем задачу которую обновляем
        $currentType = $task ? $task->type : null;

        $rules = [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'start_date'  => 'sometimes|required|date|after_or_equal:today',
            'type'        => 'sometimes|required|in:' . implode(',', TaskType::getList())
        ];

        // Если тип задачи - регулярная или текущий тип регулярный, тогда добавляем валидацию для дней регулярности
        if ($this->get('type') == TaskType::TYPE_RECURRING || $currentType == TaskType::TYPE_RECURRING) {
            $rules['recurrence'] = 'sometimes|required|array';
            $rules['recurrence.*'] = 'in:' . implode(',', WeekDays::getList());
        }

        return $rules;
    }
}
