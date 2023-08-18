<?php

namespace App\Http\Requests\Task;

use App\Constants\Other\WeekDays;
use App\Constants\Task\TaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'name'                   => [
                'required',
                'string',
                'max:255',
                Rule::unique('tasks', 'name')->where(function ($query) {
                    return $query->where('start_date', $this->start_date);
                }),
            ],
            'description'            => 'nullable|string',
            'start_date'             => 'required|date|after_or_equal:today',
            'type'                   => 'required|integer|in:' . implode(',', TaskType::getList()), // добавляем валидацию для поля type
            'recurrence'             => 'required_if:type,' . TaskType::TYPE_RECURRING . '|nullable|array',
            'recurrence.*'           => 'in:' . implode(',', WeekDays::getList()),
            'subtasks'               => 'array|nullable',
            'subtasks.*.name'        => 'required|string|max:255',
            'subtasks.*.description' => 'string|nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле Имя обязательно для заполнения.',
            'name.string'   => 'Поле Имя должно быть строкой.',
            'name.max'      => 'Поле Имя не может быть длиннее 255 символов.',
            'name.unique'   => 'Задача с таким именем уже существует на указанную дату.',

            'description.string' => 'Поле Описание должно быть строкой.',

            'start_date.required'       => 'Поле Дата начала обязательно для заполнения.',
            'start_date.date'           => 'Поле Дата начала должно быть датой.',
            'start_date.after_or_equal' => 'Поле Дата начала не может быть раньше текущей даты.',

            'type.required' => 'Поле Тип обязательно для заполнения.',
            'type.integer'  => 'Поле Тип должно быть целым числом.',
            'type.in'       => 'Выбранное значение для поля Тип недопустимо.',

            'recurrence.required_if' => 'Поле Повторение обязательно для заполнения, если тип - повторяющийся.',
            'recurrence.array'       => 'Поле Повторение должно быть массивом.',

            'recurrence.*.in' => 'Выбранное значение для поля Повторение недопустимо.',

            'subtasks.array' => 'Поле Подзадачи должно быть массивом.',

            'subtasks.*.name.required' => 'Поле Имя подзадачи обязательно для заполнения.',
            'subtasks.*.name.string'   => 'Поле Имя подзадачи должно быть строкой.',
            'subtasks.*.name.max'      => 'Поле Имя подзадачи не может быть длиннее 255 символов.',

            'subtasks.*.description.string' => 'Поле Описание подзадачи должно быть строкой.',
        ];
    }

}
