<?php

namespace App\Http\Requests\Task;

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
            'status'                 => 'required|integer',
            'subtasks'               => 'array|nullable',
            'subtasks.*.name'        => 'required|string|max:255',
            'subtasks.*.description' => 'string|nullable',
            'subtasks.*.status'      => 'required|integer',
        ];
    }
}
