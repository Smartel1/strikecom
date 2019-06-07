<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventIndexRequest
 * @description Запрос на получение списка событий (применима фильтрация), сортировка по полю date
 * @summary Получить список событий
 * @filters Необязательный массив фильтров
 * @filters.tag_id индентификатор тэга, который содержится в событиях
 * @filters.conflict_ids индентификаторы конфликтов, к которым относится событие
 * @filters.date_from дата, начиная с которой выводить события
 * @filters.date_to дата, до которой выводить события
 * @filters.event_status_ids массив ид. статусов события
 * @filters.event_type_ids массив ид. типов события
 * @filters.favourites только избранные
 * @filters.published только опубликованные (true), только неопубликованные (false), любые (если не передать)
 * @per_page Количество элементов в пагинации (по умолчанию 20)
 * @page Страница пагинации
 * @package App\Http\Requests
 */
class EventIndexRequest extends FormRequest
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
            'filters'                     => 'nullable|array',
            'filters.tag_id'              => 'nullable|integer',
            'filters.conflict_ids'        => 'nullable|array',
            'filters.conflict_ids.*'      => 'integer',
            'filters.date_from'           => 'integer',
            'filters.date_to'             => 'integer',
            'filters.event_status_ids'    => 'array',
            'filters.event_status_ids.*'  => 'integer',
            'filters.event_type_ids'      => 'array',
            'filters.event_type_ids.*'    => 'integer',
            'filters.favourites'          => 'boolean',
            'filters.published'           => 'boolean',
            'per_page'                    => 'integer|min:1',
            'page'                        => 'integer',
        ];
    }
}
