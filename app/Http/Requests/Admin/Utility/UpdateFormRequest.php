<?php

namespace App\Http\Requests\Admin\Utility;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' =>  'required|string',
            'command'   =>  'required|string'
        ];
    }

    public function messages()
    {
        return [
            'title.required'   =>  'Укажите название проекта',
            'command.required'  =>  'Укажите URL проекта',
            'title.alpha'   =>  'Название должно содержать только буквы',
            'title.unique'  =>  'Проект с таким названием уже есть',
        ];
    }
}
