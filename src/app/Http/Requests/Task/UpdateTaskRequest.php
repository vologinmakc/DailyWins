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

    public function messages()
    {
        return [
            'name.required'             => 'Поле "Имя" обязательно для заполнения.',
            'name.string'               => 'Поле "Имя" должно быть строкой.',
            'name.max'                  => 'Поле "Имя" не должно превышать 255 символов.',
            'description.string'        => 'Поле "Описание" должно быть строкой.',
            'start_date.required'       => 'Поле "Дата начала" обязательно для заполнения.',
            'start_date.date'           => 'Поле "Дата начала" должно быть датой.',
            'start_date.after_or_equal' => 'Поле "Дата начала" должно быть равной или позже текущей даты.',
            'type.required'             => 'Поле "Тип задачи" обязательно для заполнения.',
            'type.in'                   => 'Выбранный "Тип задачи" недопустим.',
            'recurrence.required'       => 'Поле "Дни регулярности" обязательно для заполнения, так как тип задачи регулярный.',
            'recurrence.array'          => 'Поле "Дни регулярности" должно быть массивом.',
            'recurrence.*.in'           => 'Выбранный день регулярности недопустим.',
        ];
    }
}
