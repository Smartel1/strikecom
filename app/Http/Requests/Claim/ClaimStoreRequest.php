<?php

namespace App\Http\Requests\Claim;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ClaimStoreRequest
 * @description Запрос на создание жалобы на комментарий
 * @summary Создать жалобу
 * @claim_type_id ид. типа жалобы
 * @package App\Http\Requests\Comment
 */
class ClaimStoreRequest extends FormRequest
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
            'claim_type_id' => 'required|exists:App\Entities\References\ClaimType,id',
        ];
    }

    public function attributes()
    {
        return [
            'claim_type_id' => 'ид. типа жалобы',
        ];
    }
}
