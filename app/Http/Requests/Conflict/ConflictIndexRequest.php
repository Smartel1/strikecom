<?php

namespace App\Http\Requests\Conflict;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ConflictIndexRequest
 * @description Запрос на получение списка конфликтов
 * @summary Получение списка конфликтов
 * @brief Если true, то вернутся только поля id, name
 * @filters Фильтры
 * @filters.conflict_result_ids массив ид. результатов
 * @filters.conflict_reason_ids массив ид. причин
 * @filters.ancestors_of ид. дочернего конфликта (для получения всех его предков)
 * @filters.children_of ид. родительского конфликта (для получения его непосредственных детей)
 * @filters.date_from дата, начиная с которой выводить события
 * @filters.date_to дата, до которой выводить события
 * @filters.near массив, содержащий обязательные ключи: lat, lng, radius (в километрах)
 * @filters.near.lat широта точки (в градусах)
 * @filters.near.lng долгота точки (в градусах)
 * @filters.near.radius радиус поиска в километрах
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
            'brief'                         => 'nullable|boolean',
            'filters'                       => 'nullable|array',
            'filters.date_from'             => 'nullable|integer',
            'filters.date_to'               => 'nullable|integer',
            'filters.conflict_result_ids'   => 'array',
            'filters.conflict_result_ids.*' => 'integer',
            'filters.conflict_reason_ids'   => 'array',
            'filters.conflict_reason_ids.*' => 'integer',
            'filters.ancestors_of'          => 'nullable|integer|exists:App\Entities\Conflict,id',
            'filters.children_of'           => 'nullable|integer|exists:App\Entities\Conflict,id',
            'filters.near'                  => 'array',
            'filters.near.lat'              => 'numeric|required_with:filters.near',
            'filters.near.lng'              => 'numeric|required_with:filters.near',
            'filters.near.radius'           => 'integer|required_with:filters.near',
        ];
    }
}
