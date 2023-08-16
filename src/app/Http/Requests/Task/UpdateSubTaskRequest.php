<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|required|integer',
        ];
    }

    public function messages()
    {
        return [
            'name.required'      => 'Поле "Имя" обязательно для заполнения.',
            'name.string'        => 'Поле "Имя" должно быть строкой.',
            'name.max'           => 'Поле "Имя" не должно превышать 255 символов.',
            'description.string' => 'Поле "Описание" должно быть строкой.',
            'status.required'    => 'Поле "Статус" обязательно для заполнения.',
            'status.integer'     => 'Поле "Статус" должно быть числом.',
        ];
    }
}
