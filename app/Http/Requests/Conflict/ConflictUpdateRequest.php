<?php

namespace App\Http\Requests\Conflict;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConflictUpdateRequest
 * @description Запрос на обновление конфликта
 * @summary Обновление конфликта
 * @title Заголовок
 * @latitude Долгота
 * @longitude Широта
 * @company_name Наименование предприятия
 * @date_from Дата начала конфликта
 * @date_to Дата окончания конфликта
 * @conflict_reason_id Идентификатор причины конфликта
 * @conflict_result_id Идентификатор результата конфликта
 * @industry_id Идентификатор отрасли
 * @region_id Идентификатор региона
 * @package App\Http\Requests\Conflict
 */
class ConflictUpdateRequest extends FormRequest
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
            'title'         => 'nullable|string|max:255',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'company_name'  => 'nullable|string|min:3|max:500',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date',
            'conflict_reason_id'     => 'nullable|exists:conflict_reasons,id',
            'conflict_result_id'     => 'nullable|exists:conflict_results,id',
            'industry_id'            => 'nullable|exists:industries,id',
            'region_id'              => 'nullable|exists:regions,id',
        ];
    }
}
