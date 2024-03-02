<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateResourceModuleUsingAIPreviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $base_rules = [
            'title'                             => 'required',
            'organization_id'                   => 'required|exists:organizations,uuid',
            'description'                       => 'required',
            'category'                          => 'required|exists:categories,title',
            'duration'                          => 'required|exists:durations,title',
            'level'                             => 'required|exists:levels,title',
            'skill_titles'                      => 'required|array',
            'resource_modules'                  => 'nullable',
            'resource_module_prepr'             => 'nullable',
            'resource_module_openai'            => 'nullable',
            'resource_module_go1'               => 'nullable',
            'openai_resource_module_types'      => 'nullable|array',
            'openai_resource_module_types.*'    => 'in:videos,links',
            'go1_resource_module_types'         => 'nullable|array',
            'go1_resource_module_types.*'       => 'in:videos,links',
            'is_ai_created'                     => 'in:yes,no',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'title.required'                        => __('responses.title_required'),
            'description.required'                  => __('responses.description_required'),
            'organization_id.required'              => __('responses.organization_id_required'),
            'organization_id.exists'                => __('responses.organization_not_exists'),
            'openai_resource_module_types.in'       => __('responses.type_fail_in'),
            'go1_resource_module_types.in'          => __('responses.type_fail_in'),
            'category.required'                     => __('responses.category_missing'),
            'category.exists'                       => __('responses.category_exists'),
            'duration.required'                     => __('responses.duration_required'),
            'duration.exists'                       => __('responses.duration_exists'),
            'level.required'                        => __('responses.level_required'),
            'level.exists'                          => __('responses.level_exists'),
            'skill_titles'                          => __('responses.skill_titles_missing'),
            'is_ai_created'                         => __('responses.choose_yes_no'),
        ];
    }
}
