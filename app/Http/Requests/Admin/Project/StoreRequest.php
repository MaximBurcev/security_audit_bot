<?php

namespace App\Http\Requests\Admin\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'title' =>  'required|string|unique:projects',
            'url'   =>  'required|url|unique:projects'
        ];
    }

    public function messages()
    {
        return [
            'title.required'   =>  'Укажите название проекта',
            'url.required'  =>  'Укажите URL проекта',
            'title.alpha'   =>  'Название должно содержать только буквы',
            'title.unique'  =>  'Проект с таким названием уже есть',
            'url.unique'    =>  'Проект с таким URL уже существует'
        ];
    }
}
