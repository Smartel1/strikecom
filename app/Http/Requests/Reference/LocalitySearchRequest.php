<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description Запрос на получение нас. пунктов по поисковой строке и принадлежности к региону
 * @summary Найти населенные пункты
 * @name Название населенного пункта
 * @region_id Необязательный идентификатор региона
 * @package App\Http\Requests
 */
class LocalitySearchRequest extends FormRequest
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
            'name'      => 'required|string|min:2',
            'region_id' => 'numeric'
        ];
    }
}
