<?php

namespace App\Http\Requests\Manage\Challenge;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateChallengeFromResourceUsingAIPreviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $base_rules = [
            'resource_modules'                      => 'required|array',
            'resource_modules.*'                    => 'exists:resource_modules,uuid',
            'organization_id'                       => 'required|exists:organizations,uuid',
            'additional_information'                => 'nullable',
            'is_ai_created'                         => 'required|boolean',
        ];

        return $base_rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }

    public function message()
    {
        return [
            'resource_modules.required'             => __('responses.resource_modules_required'),
            'resource_modules.array'                => __('responses.resource_modules_array'),
            'resource_modules.*.exists'             => __('responses.resource_modules_exists'),
            'organization_id.required'              => __('responses.organization_id_required'),
            'organization_id.exists'                => __('responses.organization_not_found'),
            'is_ai_created.required'                => __('responses.is_ai_created_required'),
            'is_ai_created.boolean'                 => __('responses.true_or_false'),
        ];
    }
}
