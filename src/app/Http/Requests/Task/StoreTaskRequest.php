<?php

namespace App\Http\Requests\Task;

use App\Constants\Other\WeekDays;
use App\Constants\Task\TaskType;
use Illuminate\Foundation\Http\FormRequest;

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
            'name'                   => 'required|string|max:255',
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
}
