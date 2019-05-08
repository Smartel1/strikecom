<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventIndexRequest
 * @description Запрос на получение списка событий (применима фильтрация), сортировка по полю date
 * @summary Получить список событий
 * @filters Необязательный массив фильтров. Может содержать ключ conflict_id, чтобы вывести только привязанные
 * к конкретному конфликту новости и tag_id для вывода новостей с тегом
 * @filters.tag_id индентификатор тэга, который содержится в событиях
 * @filters.conflict_ids индентификаторы конфликтов, к которым относится событие
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
            'filters'                => 'nullable|array',
            'filters.tag_id'         => 'nullable|integer',
            'filters.conflict_ids'   => 'nullable|array',
            'filters.conflict_ids.*' => 'integer',
            'per_page'               => 'integer|min:1',
            'page'                   => 'integer',
        ];
    }
}
