<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description Запрос на создание региона
 * @summary Сохранить название региона
 * @name Имя региона
 * @country_id Ид. страны
 * @package App\Http\Requests
 */
class RegionStoreRequest extends FormRequest
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
            'name'       => 'required|string|min:1',
            'country_id' => 'required|exists:App\Entities\References\Country,id',
        ];
    }
}
