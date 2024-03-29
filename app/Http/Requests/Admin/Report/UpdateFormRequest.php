<?php

namespace App\Http\Requests\Admin\Report;

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
            'status'     => 'required|string',
            'utility_id' => 'required|integer',
            'project_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'status.required'     => 'Укажите статус отчета',
            'content.required'    => 'Контент не указан',
            'utility_id.required' => 'Утилита не выбрана',
            'project_id.required' => 'Проект не указан'
        ];
    }
}
