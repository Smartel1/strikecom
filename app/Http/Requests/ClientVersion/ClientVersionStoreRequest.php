<?php

namespace App\Http\Requests\ClientVersion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ClientVersionStoreRequest
 * @description Запрос на создание версии клиентского приложения
 * @summary Создание версии клиентского приложения
 * @current_version Текущая версия приложения
 * @client_id Ид. клиента
 * @required Обязательность обновления
 * @description_ru Описание на русском
 * @description_en Описание на английском
 * @description_es Описание на испанском
 * @package App\Http\Requests\ClientVersion
 */
class ClientVersionStoreRequest extends FormRequest
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
            'client_id'      => 'required|integer',
            'version'        => 'required|string',
            'required'       => 'required|boolean',
            'description_ru' => 'required|string|max:500',
            'description_en' => 'required|string|max:500',
            'description_es' => 'required|string|max:500',
        ];
    }
}
