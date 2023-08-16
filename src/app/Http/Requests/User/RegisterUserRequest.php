<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'     => 'required|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'captcha'  => 'required|custom_captcha:captcha_token'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле Имя обязательно для заполнения.',
            'name.max' => 'Поле Имя не может быть длиннее 255 символов.',

            'email.required' => 'Поле Электронная почта обязательно для заполнения.',
            'email.email' => 'Поле Электронная почта должно быть действительным электронным адресом.',
            'email.unique' => 'Электронный адрес уже зарегистрирован.',

            'password.required' => 'Поле Пароль обязательно для заполнения.',
            'password.min' => 'Поле Пароль должно содержать не менее 6 символов.',

            'captcha.required' => 'Поле Капча обязательно для заполнения.',
            'captcha.custom_captcha' => 'Введенная капча неверна.',
        ];
    }
}
