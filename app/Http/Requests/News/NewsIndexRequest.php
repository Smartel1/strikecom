<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NewsIndexRequest
 * @description Запрос на получение списка новостей (применима фильтрация), сортировка по полю date
 * @summary Получить список новостей
 * @filters Необязательный массив фильтров
 * @filters.tag_id индентификатор тэга, который содержится в новостях
 * @filters.date_from дата, начиная с которой выводить события
 * @filters.date_to дата, до которой выводить события
 * @filters.favourites только избранные
 * @filters.published только опубликованные (true), только неопубликованные (false), любые (если не передать)
 * @per_page Количество элементов в пагинации (по умолчанию 20)
 * @page Страница пагинации
 * @package App\Http\Requests\News
 */
class NewsIndexRequest extends FormRequest
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
            'filters'            => 'array',
            'filters.tag_id'     => 'integer',
            'filters.date_from'  => 'integer',
            'filters.date_to'    => 'integer',
            'filters.favourites' => 'boolean',
            'filters.published'  => 'boolean',
            'per_page'           => 'integer|min:1',
            'page'               => 'integer|min:1',
        ];
    }
}
