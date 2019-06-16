<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @class SubscribeRequest
 * @description Запрос на подписку
 * @summary Подписаться на push-уведомления
 * @package App\Http\Requests
 * @state Состояние подписки (подписан/не подписан)
 * @fcm fcm-token клиента
 */
class SubscribeRequest extends FormRequest
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
            'state' => 'boolean|required',
            'fcm'   => 'string|required_if:state,1'
        ];
    }
}
