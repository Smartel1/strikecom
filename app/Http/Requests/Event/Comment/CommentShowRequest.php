<?php

namespace App\Http\Requests\Event\Comment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CommentShowRequest
 * @description Получить комментарий к событию
 * @summary Найти комментарий
 * @package App\Http\Requests\Event\Comment
 */
class CommentShowRequest extends FormRequest
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
