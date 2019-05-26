<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class NewsSetFavouriteRequest
 * @description Запрос на отметку новости избранной или отмены
 * @summary Изменение списка избранного
 * @favourite флаг - в избранном или нет
 * @package App\Http\Requests\Event
 */
class NewsSetFavouriteRequest extends FormRequest
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
            'favourite' => 'required|boolean',
        ];
    }
}
