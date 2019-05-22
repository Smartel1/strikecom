<?php

namespace App\Http\Requests\Conflict;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConflictIndexRequest
 * @description Запрос на получение списка конфликтов
 * @summary Получение списка конфликтов
 * @brief Если true, то вернутся только поля id, name
 * @filters Фильтры (date_from, date_to, ancestors_of)
 * @package App\Http\Requests\Conflict
 */
class ConflictIndexRequest extends FormRequest
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
            'brief'                => 'nullable|boolean',
            'filters'              => 'nullable|array',
            'filters.date_from'    => 'nullable|integer',
            'filters.date_to'      => 'nullable|integer',
            'filters.ancestors_of' => 'nullable|integer|exists:App\Entities\Conflict,id',
        ];
    }
}
