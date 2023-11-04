<?php

namespace App\Http\Requests\Admin\Audit;

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
            'title'     => 'required|string',
            'user_id'   => 'required|integer',
            'report_id' => 'required|array'
        ];
    }

    public function messages()
    {
        return [
            'title.required'     => 'Укажите название аудита',
            'user_id.required'   => 'Пользователь не указан',
            'report_id.required' => 'Отчеты не указаны'
        ];
    }
}
