<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @description Запрос на получение стран по поисковой строке
 * @summary Найти страны
 * @name Имя страны
 * @package App\Http\Requests
 */
class CountrySearchRequest extends FormRequest
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
            'name' => 'required|string|min:2'
        ];
    }
}
