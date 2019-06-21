<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description Запрос на получение регионов по поисковой строке и принадлежности к стране
 * @summary Найти регионы
 * @name Название региона
 * @country_id Необязательный идентификатор страны
 * @package App\Http\Requests
 */
class RegionSearchRequest extends FormRequest
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
            'name'       => 'required|string|min:2',
            'country_id' => 'numeric'
        ];
    }
}
