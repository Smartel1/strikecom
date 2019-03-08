<?php

namespace App\Http\Requests\Conflict;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConflictUpdateRequest
 * @description Запрос на обновление конфликта
 * @summary Обновление конфликта
 * @title Заголовок
 * @title_ru Заголовок на русском
 * @title_en Заголовок на английском
 * @title_es Заголовок на испанском
 * @latitude Долгота
 * @longitude Широта
 * @company_name Наименование предприятия
 * @date_from Дата начала конфликта unix-timestamp
 * @date_to Дата окончания конфликта unix-timestamp
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
            'title_ru'      => 'nullable|string|max:255',
            'title_en'      => 'nullable|string|max:255',
            'title_es'      => 'nullable|string|max:255',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'company_name'  => 'nullable|string|min:3|max:500',
            'date_from'     => 'nullable|integer',
            'date_to'       => 'nullable|integer',
            'conflict_reason_id'     => 'nullable|exists:conflict_reasons,id',
            'conflict_result_id'     => 'nullable|exists:conflict_results,id',
            'industry_id'            => 'nullable|exists:industries,id',
            'region_id'              => 'nullable|exists:regions,id',
        ];
    }

    public function attributes()
    {
        return [
            'title'         => 'заголовок',
            'title_ru'      => 'заголовок на русском',
            'title_en'      => 'заголовок на ангийском',
            'title_es'      => 'заголовок на испанском',
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
