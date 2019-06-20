<?php

namespace App\Http\Requests\Conflict;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConflictStoreRequest
 * @description Запрос на создание конфликта
 * @summary Создание конфликта
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
 * @parent_event_id Идентификатор родительского события
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
            'title'              => 'nullable|string|max:255',
            'title_ru'           => 'nullable|string|max:255',
            'title_en'           => 'nullable|string|max:255',
            'title_es'           => 'nullable|string|max:255',
            'latitude'           => 'required|numeric',
            'longitude'          => 'required|numeric',
            'company_name'       => 'nullable|string|min:3|max:500',
            'date_from'          => 'nullable|integer',
            'date_to'            => 'nullable|integer',
            'conflict_reason_id' => 'required|integer|exists:App\Entities\References\ConflictReason,id',
            'conflict_result_id' => 'nullable|integer|exists:App\Entities\References\ConflictResult,id',
            'industry_id'        => 'nullable|integer|exists:App\Entities\References\Industry,id',
            'parent_event_id'    => 'nullable|integer|exists:App\Entities\Event,id',
        ];
    }

    public function attributes()
    {
        return [
            'title'              => 'заголовок',
            'title_ru'           => 'заголовок на русском',
            'title_en'           => 'заголовок на ангийском',
            'title_es'           => 'заголовок на испанском',
            'latitude'           => 'долгота',
            'longitude'          => 'широта',
            'company_name'       => 'наименование предприятия',
            'date_from'          => 'дата начала конфликта',
            'date_to'            => 'дата окончания конфликта',
            'conflict_reason_id' => 'идентификатор причины конфликта',
            'conflict_result_id' => 'идентификатор результата конфликта',
            'industry_id'        => 'идентификатор отрасли',
            'parent_event_id'    => 'идентификатор родительского события',
        ];
    }
}
