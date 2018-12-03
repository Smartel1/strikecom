<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'conflict_id'       => 'nullable|exists:conflicts,id',
            'title'             => 'required|min:3|max:255',
            'description'       => 'required|min:3|max:255',
            'content'           => 'required|min:3',
            'date'              => 'required|date',
            'source_link'       => 'nullable|string|max:500',
            'event_status_id'   => 'required|exists:event_statuses,id',
            'event_type_id'     => 'required|exists:event_types,id',
            'tags'              => 'nullable|array',
            'tags.*'            => 'string|min:2|max:20',
            'image_urls'        => 'nullable|array',
            'image_urls.*'      => 'string|max:500',
        ];
    }
}
