<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @class SubscribeRequest
 * @description Запрос на обновление пользователя (менять роли могут только модераторы или админы, обычные пользователи могут менять только свои записи)
 * @summary Обновить пользователя
 * @package App\Http\Requests
 * @fcm fcm-token клиента
 * @roles роли
 */
class UserUpdateRequest extends FormRequest
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
            'fcm'   => 'string',
            'roles' => 'array'
        ];
    }
}
