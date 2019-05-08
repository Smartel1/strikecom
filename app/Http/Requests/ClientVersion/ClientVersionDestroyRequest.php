<?php

namespace App\Http\Requests\ClientVersion;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ClientVersionDestroyRequest
 * @description Запрос на удаление версии клиентского приложения
 * @summary Удалить версию клиентского приложения
 * @package App\Http\Requests\ClientVersion
 */
class ClientVersionDestroyRequest extends FormRequest
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
        return [];
    }
}
