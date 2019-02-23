<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NewsIndexRequest
 * @description Запрос на получение списка новостей (применима фильтрация)
 * @summary Получить список событий
 * @filters Необязательный массив фильтров. Может содержать ключ tag_id для вывода новостей с тегом
 * @filters.tag_id индентификатор тэга, который содержится в событиях
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
            'filters'        => 'nullable|array',
            'filters.tag_id' => 'nullable|integer',
            'per_page'       => 'integer|min:1',
            'page'           => 'integer',
        ];
    }
}
