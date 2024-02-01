<?php

namespace App\Http\Requests\Admin\Task;

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
            'title'       => 'required|string',
            'report_id'   => 'required|integer',
            'cron_format' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required'       => 'Название задачи не указано',
            'report_id.required'   => 'Вы не выбрали отчет',
            'cron_format.required' => 'Введите интервал выполнения задачи в cron-формате',
        ];
    }
}
