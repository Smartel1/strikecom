<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventIndexRequest
 * @description Запрос на получение списка событий (применима фильтрация)
 * @summary Получить список событий
 * @filters Необязательный массив фильтров. Может содержать ключ conflict_id, чтобы вывести только привязанные
 * к конкретному конфликту новости
 * @package App\Http\Requests
 */
class EventIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filters' => 'nullable|array',
        ];
    }
}
