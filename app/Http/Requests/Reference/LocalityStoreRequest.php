<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description Запрос на создание населенного пункта
 * @summary Сохранить название населенного пункта
 * @name Имя населенного пункта
 * @region_id Ид. региона
 * @package App\Http\Requests
 */
class LocalityStoreRequest extends FormRequest
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
            'name'      => 'required|string|min:1',
            'region_id' => 'required|exists:App\Entities\References\Region,id',
        ];
    }
}
