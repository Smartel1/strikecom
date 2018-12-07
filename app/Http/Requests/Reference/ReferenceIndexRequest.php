<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventIndexRequest
 * @description Запрос на получение справочников
 * @summary Получить справочники
 * @package App\Http\Requests
 */
class ReferenceIndexRequest extends FormRequest
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
