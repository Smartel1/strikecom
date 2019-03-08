<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NewsStoreRequest
 * @description Запрос на создание новости
 * @summary Создание новости
 * @title заголовок
 * @title_ru Заголовок на русском
 * @title_en Заголовок на английском
 * @title_es Заголовок на испанском
 * @content тело события
 * @content_ru тело на русском
 * @content_en тело на английском
 * @content_es тело на испанском
 * @date дата unix-timestamp
 * @source_link ссылка на источник
 * @tags массив тэгов
 * @photo_urls массив ссылок на фото
 * @videos массив видео
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
            'title'                 => 'nullable|string|max:255',
            'title_ru'              => 'nullable|string|max:255',
            'title_en'              => 'nullable|string|max:255',
            'title_es'              => 'nullable|string|max:255',
            'content'               => 'nullable|string|min:3',
            'content_ru'            => 'nullable|string|min:3',
            'content_en'            => 'nullable|string|min:3',
            'content_es'            => 'nullable|string|min:3',
            'date'                  => 'required|integer',
            'source_link'           => 'nullable|string|max:500',
            'tags'                  => 'nullable|array',
            'tags.*'                => 'string|min:2|max:20',
            'photo_urls'            => 'nullable|array',
            'photo_urls.*'          => 'required|string|max:500',
            'videos'                => 'nullable|array',
            'videos.*.url'          => 'required|string|max:500',
            'videos.*.preview_url'  => 'nullable|string|max:500',
            'videos.*.video_type_id'=> 'exists:video_types,id',
        ];
    }
}
