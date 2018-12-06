<?php

namespace App\Http\Requests\Conflict;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConflictStoreRequest
 * @description Запрос на создание конфликта
 * @summary Создание конфликта
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
class ConflictStoreRequest extends FormRequest
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
            'title'         => 'required|string|max:255',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'company_name'  => 'required|string|min:3|max:500',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date',
            'conflict_reason_id'     => 'required|exists:conflict_reasons,id',
            'conflict_result_id'     => 'nullable|exists:conflict_results,id',
            'industry_id'            => 'nullable|exists:industries,id',
            'region_id'              => 'nullable|exists:regions,id',
        ];
    }

    public function attributes()
    {
        return [
            'title'         => 'заголовок',
            'latitude'      => 'долгота',
            'longitude'     => 'широта',
            'company_name'  => 'наименование предприятия',
            'date_from'     => 'дата начала конфликта',
            'date_to'       => 'дата окончания конфликта',
            'conflict_reason_id'     => 'идентификатор причины конфликта',
            'conflict_result_id'     => 'идентификатор результата конфликта',
            'industry_id'            => 'идентификатор отрасли',
            'region_id'              => 'идентификатор региона',
        ];
    }
}
