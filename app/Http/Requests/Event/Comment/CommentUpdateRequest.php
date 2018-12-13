<?php

namespace App\Http\Requests\Event\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CommentUpdateRequest
 * @description Запрос на изменение комментария
 * @summary Обновление комментария
 * @content содержание
 * @image_urls массив ссылок на картинки
 * @package App\Http\Requests\Event\Comment
 */
class CommentUpdateRequest extends FormRequest
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
            'content'       => 'nullable|min:3',
            'image_urls'    => 'nullable|array',
            'image_urls.*'  => 'string|max:500',
        ];
    }
}
