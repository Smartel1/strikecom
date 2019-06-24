<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description Запрос на создание страны
 * @summary Сохранить название страны
 * @name_ru Имя страны на русском
 * @name_en Имя страны на английском
 * @name_es Имя страны на испанском
 * @package App\Http\Requests
 */
class CountryStoreRequest extends FormRequest
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
            'name_ru' => 'required|string|min:1',
            'name_en' => 'required|string|min:1',
            'name_es' => 'required|string|min:1',
        ];
    }
}
