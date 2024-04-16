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
            'challengeTitle'                        => 'required',
            'organization_id'                       => 'required|exists:organizations,uuid',
            'description'                           => 'nullable',
            'category_id'                           => 'nullable|exists:categories,id',
            'duration_id'                           => 'required|exists:durations,id',
            'level_id'                              => 'required|exists:levels,id',
            'skills'                                => 'required|array',
            'skills.*'                              => 'numeric|exists:skills,id',
            'jobs'                                  => 'required|array',
            'jobs.*'                                => 'numeric|exists:job_titles,id',
            'steps'                                 => 'nullable|array',
            'reflections'                           => 'nullable|array',
            'is_ai_created'                         => 'required|boolean',
            'resource_modules'                      => 'required|boolean',
            'resource_module_openai'                => 'nullable|boolean',
            'openai_resource_module_types'          => 'nullable|array',
            'resource_module_go1'                   => 'nullable|boolean',
            'go1_resource_module_types'             => 'nullable|array',
            'resource_module_prepr'                 => 'nullable|boolean',
            'skill_titles'                          => 'nullable|array',
            'job_titles'                            => 'nullable|array',
            'level'                                 => 'required',
            'duration'                              => 'required',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'title.required_if'                     => __('responses.title_required'),
            'organization_id.required'              => __('responses.organization_id_required'),
            'organization_id.exists'                => __('responses.organization_not_found'),
            'category_id.exists'                    => __('responses.category_not_found'),
            'duration_id.required'                  => __('responses.duration_id_required'),
            'duration_id.exists'                    => __('responses.duration_id_exists'),
            'level_id.required'                     => __('responses.level_id_required'),
            'level_id.exists'                       => __('responses.level_id_exists'),
            'skills.array'                          => __('responses.skills_array'),
            'skills.*.numeric'                      => __('responses.skills_numeric'),
            'skills.*.exists'                       => __('responses.skill_not_exists'),
            'skills.required'                       => __('responses.skills_required'),
            'jobs.array'                            => __('responses.jobs_array'),
            'jobs.*.numeric'                        => __('responses.jobs_numeric'),
            'jobs.*.exists'                         => __('responses.job_not_exists'),
            'jobs.required'                         => __('responses.jobs_required'),
            'steps'                                 => __('responses.steps_array'),
            'reflections'                           => __('responses.reflections_array'),
            'is_ai_created'                         => __('responses.true_or_false'),
            'resource_modules.required'             => __('responses.resource_modules_required'),
            'resource_modules.boolean'              => __('responses.true_or_false'),
            'resource_module_openai'                => __('responses.true_or_false'),
            'openai_resource_module_types'          => __('responses.openai_resource_module_types_array'),
            'resource_module_go1'                   => __('responses.true_or_false'),
            'go1_resource_module_types'             => __('responses.go1_resource_module_types_array'),
            'resource_module_prepr'                 => __('responses.true_or_false'),
            'skill_titles'                          => __('responses.skill_titles_array'),
            'job_titles'                            => __('responses.job_titles_array'),
            'level'                                 => __('responses.level_requireds'),
            'duration'                              => __('responses.duration_required'),
        ];
    }
}
