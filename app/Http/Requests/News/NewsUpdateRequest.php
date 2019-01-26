<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EventRequest
 * @summary Обновление новости
 * @title заголовок
 * @content тело новости
 * @date дата unix-timestamp
 * @source_link ссылка на источник
 * @event_status_id ссылка на статус новости
 * @event_type_id ссылка на тип новости
 * @tags массив тэгов
 * @image_urls массив ссылок на изображения
 * @package App\Http\Requests\News
 */
class NewsUpdateRequest extends FormRequest
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
            'title'             => 'nullable|min:3|max:255',
            'content'           => 'nullable|min:3',
            'date'              => 'nullable|integer',
            'source_link'       => 'nullable|string|max:500',
            'event_status_id'   => 'nullable|exists:event_statuses,id',
            'event_type_id'     => 'nullable|exists:event_types,id',
            'tags'              => 'nullable|array',
            'tags.*'            => 'string|min:2|max:20',
            'image_urls'        => 'nullable|array',
            'image_urls.*'      => 'string|max:500',
        ];
    }
}
