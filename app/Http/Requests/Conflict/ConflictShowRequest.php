<?php

namespace App\Http\Requests\Conflict;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConflictShowRequest
 * @description Запрос на получение события
 * @summary Найти событие
 * @package App\Http\Requests\Conflict
 */
class ConflictShowRequest extends FormRequest
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
