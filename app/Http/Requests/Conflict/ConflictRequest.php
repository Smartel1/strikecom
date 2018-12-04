<?php

namespace App\Http\Requests\Conflict;

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
            'latitude'      => 'required|numeric',
            'longitude'     => 'required|numeric',
            'company_name'  => 'required|string|min:3|max:500',
            'date_from'     => 'nullable|date',
            'date_to'       => 'nullable|date',
            'conflict_reason_id'     => 'required|exists:conflict_reasons,id',
            'conflict_result_id'     => 'nullable|exists:conflict_results,id',
            'industry_id'            => 'nullable|exists:industries,id',
            'region_id'              => 'nullable|exists:regions,id',
        ];
    }
}
