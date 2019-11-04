<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CommentUpdateRequest
 * @description Запрос на изменение комментария
 * @summary Обновление комментария
 * @content содержание
 * @photo_urls массив ссылок на фото
 * @package App\Http\Requests\Comment
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
            'content'      => 'required|string|min:1',
            'photo_urls'   => 'array',
            'photo_urls.*' => 'string|max:500',
        ];
    }
}
