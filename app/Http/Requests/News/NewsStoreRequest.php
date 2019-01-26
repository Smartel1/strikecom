<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NewsStoreRequest
 * @description Запрос на создание новости
 * @summary Создание новости
 * @title заголовок
 * @content тело события
 * @date дата unix-timestamp
 * @source_link ссылка на источник
 * @tags массив тэгов
 * @image_urls массив ссылок на изображения
 * @package App\Http\Requests\News
 */
class NewsStoreRequest extends FormRequest
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
            'title'             => 'required|min:3|max:255',
            'content'           => 'required|min:3',
            'date'              => 'required|integer',
            'source_link'       => 'nullable|string|max:500',
            'tags'              => 'nullable|array',
            'tags.*'            => 'string|min:2|max:20',
            'image_urls'        => 'nullable|array',
            'image_urls.*'      => 'string|max:500',
        ];
    }
}
