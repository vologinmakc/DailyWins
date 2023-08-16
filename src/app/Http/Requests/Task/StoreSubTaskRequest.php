<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_id'     => 'required|integer|exists:tasks,id'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле Имя обязательно для заполнения.',
            'name.string'   => 'Поле Имя должно быть строкой.',
            'name.max'      => 'Поле Имя не может быть длиннее 255 символов.',

            'description.string' => 'Поле Описание должно быть строкой.',

            'task_id.required' => 'Поле ID задачи обязательно для заполнения.',
            'task_id.integer'  => 'Поле ID задачи должно быть целым числом.',
            'task_id.exists'   => 'Задачи с таким ID не существует.',
        ];
    }

}
