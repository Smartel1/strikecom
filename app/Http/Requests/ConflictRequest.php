<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConflictRequest extends FormRequest
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
            'name'          => 'required|min:3',
            'description'   => 'required|min:3',
            'content'       => 'required|min:3',
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date',
            'source_link'   => 'nullable|min:3',
            'conflict_status_id'     => 'required|exists:conflict_statuses,id',
            'conflict_type_id'       => 'required|exists:conflict_types,id',
            'conflict_reason_id'     => 'required|exists:conflict_reasons,id',
            'conflict_result_id'     => 'required|exists:conflict_results,id',
            'industry_id'            => 'required|exists:industries,id',
            'region_id'              => 'required|exists:regions,id',
        ];
    }
}
