<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventStoreRequest
 * @description Запрос на создание события
 * @summary Создание события
 * @conflict_id ид. конфликта
 * @published опубликована ли запись
 * @title заголовок
 * @title_ru Заголовок на русском
 * @title_en Заголовок на английском
 * @title_es Заголовок на испанском
 * @content тело события
 * @content_ru тело на русском
 * @content_en тело на английском
 * @content_es тело на испанском
 * @date дата unix-timestamp
 * @latitude Долгота
 * @longitude Широта
 * @source_link ссылка на источник
 * @locality_id ссылка на населенный пункт
 * @event_status_id ссылка на статус события
 * @event_type_id ссылка на тип события
 * @tags массив тэгов
 * @photo_urls массив ссылок на фото
 * @videos массив видео
 * @package App\Http\Requests\Event
 */
class EventStoreRequest extends FormRequest
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
            'conflict_id'            => 'required|exists:App\Entities\Conflict,id',
            'published'              => 'nullable|boolean',
            'title'                  => 'nullable|string|max:255',
            'title_ru'               => 'nullable|string|max:255',
            'title_en'               => 'nullable|string|max:255',
            'title_es'               => 'nullable|string|max:255',
            'content'                => 'nullable|string|min:3',
            'content_ru'             => 'nullable|string|min:3',
            'content_en'             => 'nullable|string|min:3',
            'content_es'             => 'nullable|string|min:3',
            'date'                   => 'required|integer',
            'latitude'               => 'required|numeric',
            'longitude'              => 'required|numeric',
            'source_link'            => 'nullable|string|max:500',
            'locality_id'            => 'nullable|exists:App\Entities\References\Locality,id',
            'event_status_id'        => 'nullable|exists:App\Entities\References\EventStatus,id',
            'event_type_id'          => 'nullable|exists:App\Entities\References\EventType,id',
            'tags'                   => 'array',
            'tags.*'                 => 'string|min:2|max:20',
            'photo_urls'             => 'array',
            'photo_urls.*'           => 'required|string|max:500',
            'videos'                 => 'array',
            'videos.*.url'           => 'required|string|max:500',
            'videos.*.preview_url'   => 'nullable|string|max:500',
            'videos.*.video_type_id' => 'exists:App\Entities\References\VideoType,id',
        ];
    }
}
