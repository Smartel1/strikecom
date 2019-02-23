<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventRequest
 * @summary Обновление события
 * @title заголовок
 * @content тело события
 * @date дата unix-timestamp
 * @source_link ссылка на источник
 * @event_status_id ссылка на статус события
 * @event_type_id ссылка на тип события
 * @tags массив тэгов
 * @photo_urls массив ссылок на фото
 * @videos массив видео
 * @package App\Http\Requests\Event
 */
class EventUpdateRequest extends FormRequest
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
            'title'               => 'nullable|min:3|max:255',
            'content'             => 'nullable|min:3',
            'date'                => 'nullable|integer',
            'source_link'         => 'nullable|string|max:500',
            'event_status_id'     => 'nullable|exists:event_statuses,id',
            'event_type_id'       => 'nullable|exists:event_types,id',
            'tags'                => 'nullable|array',
            'tags.*'              => 'string|min:2|max:20',
            'photo_urls'           => 'nullable|array',
            'photo_urls.*'         => 'required|string|max:500',
            'videos'               => 'nullable|array',
            'videos.*.url'         => 'required|string|max:500',
            'videos.*.preview_url' => 'nullable|string|max:500',
        ];
    }
}
