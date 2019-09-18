<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventIndexRequest
 * @description Запрос на получение события
 * @summary Найти событие
 * @withRelatives вернуть вместе с массивом связанных событий (для отображения развития конфликта)
 * @package App\Http\Requests
 */
class EventShowRequest extends FormRequest
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
            'withRelatives' => 'boolean'
        ];
    }
}
