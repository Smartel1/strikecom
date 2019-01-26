<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CommentStoreRequest
 * @description Запрос на создание комментария
 * @summary Создать комментарий
 * @content содержание
 * @image_urls массив ссылок на картинки
 * @package App\Http\Requests\Comment
 */
class CommentStoreRequest extends FormRequest
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
            'content'       => 'required|min:3',
            'image_urls'    => 'nullable|array',
            'image_urls.*'  => 'string|max:500',
        ];
    }
}
