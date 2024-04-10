<?php

namespace App\Http\Requests\Project;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddAdditionalInfoProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $base_rules = [
            'category_id'       => 'nullable|exists:categories,id',
            'category_id.*'     => 'numeric',
            'industry_id'       => 'nullable|exists:project_industries,id',
            'industry_id.*'     => 'numeric',
            'verticals_id'      => 'nullable|exists:project_verticals,id',
            'verticals_id.*'    => 'numeric',
            'type_id'           => 'nullable|exists:project_types,id',
            'type_id.*'         => 'numeric',
            'stage_id'          => 'nullable|exists:project_stages,id',
            'stage_id.*'        => 'numeric',
            'status_id'         => 'nullable|exists:project_status,id',
            'status_id.*'       => 'numeric',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'category_id.exists'                            => __('responses.category_not_found'),
            'category_id.*.numeric'                         => __('responses.category_numeric'),
            'industry_id.exists'                            => __('responses.project_industry_not_found'),
            'industry_id.*.numeric'                         => __('responses.project_industry_numeric'),
            'verticals_id.exists'                           => __('responses.project_verticals_not_found'),
            'verticals_id.*.numeric'                        => __('responses.project_verticals_numeric'),
            'type_id.exists'                                => __('responses.project_type_not_found'),
            'type_id.*.numeric'                             => __('responses.project_type_numeric'),
            'stage_id.exists'                               => __('responses.project_stage_not_found'),
            'stage_id.*.numeric'                            => __('responses.project_stage_numeric'),
            'status_id.exists'                              => __('responses.project_status_not_found'),
            'status_id.*.numeric'                           => __('responses.project_status_numeric'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }
}
