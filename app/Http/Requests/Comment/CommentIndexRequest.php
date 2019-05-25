<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CommentIndexRequest
 * @description Получить список комментариев
 * @summary Получить список комментариев
 * @per_page Количество элементов в пагинации (по умолчанию 20)
 * @page Страница пагинации
 * @package App\Http\Requests\Comment
 */
class CommentIndexRequest extends FormRequest
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
            'per_page' => 'integer|min:1',
            'page'     => 'integer',
        ];
    }
}
