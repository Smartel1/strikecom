<?php

namespace App\Http\Requests\ClientVersion;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ClientVersionIndexRequest
 * @description Запрос на получение новых версий клиентского приложения
 * @summary Получение списка новых версий
 * @current_version Текущая версия приложения
 * @client_id Ид. клиента
 * @package App\Http\Requests\ClientVersion
 */
class ClientVersionIndexRequest extends FormRequest
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
            'current_version' => 'required|exists:client_versions,version',
            'client_id'       => 'required|exists:client_versions,client_id',
        ];
    }
}
