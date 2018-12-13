<?php

namespace App\Http\Requests\Event\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CommentIndexRequest
 * @description Получить список комментариев к событию
 * @summary Получить список комментариев
 * @package App\Http\Requests\Event\Comment
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
        ];
    }
}
