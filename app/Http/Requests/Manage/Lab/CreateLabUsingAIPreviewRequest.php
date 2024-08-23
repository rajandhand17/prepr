<?php

namespace App\Http\Requests\Manage\Lab;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateLabUsingAIPreviewRequest extends FormRequest
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
            'duration_id'                           => 'required',
            'duration_id.*.key'                     => Rule::exists('durations', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'level_id'                              => 'required',
            'level_id.*.key'                        => Rule::exists('levels', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'additional_information'                => 'nullable',
            'is_ai_created'                         => 'required|boolean',
            'skills'                                => 'required|array',
            'skills.*.key'                          => 'numeric|'.Rule::exists('skills', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'jobs'                                  => 'required|array',
            'jobs.*.key'                            => 'numeric|'.Rule::exists('job_titles', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'resource_modules'                      => 'nullable|boolean',
            'resource_module_openai'                => 'nullable|boolean',
            'openai_resource_module_types'          => 'nullable|array',
            'openai_resource_module_types.*'        => 'in:links,videos',
            'resource_module_go1'                   => 'nullable|boolean',
            'go1_resource_module_types'             => 'nullable|array',
            'go1_resource_module_types.*'           => 'in:course,award,playlist,document,link,interactive,text,video,audio,integration',
            'resource_module_prepr'                 => 'nullable|boolean',
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
            'duration_id.required'                  => __('responses.duration_id_required'),
            'duration_id.*.exists'                  => __('responses.duration_id_exists'),
            'level_id.required'                     => __('responses.level_id_required'),
            'level_id.*.exists'                     => __('responses.level_id_exists'),
            'is_ai_created.required'                => __('responses.is_ai_created_required'),
            'is_ai_created.boolean'                 => __('responses.true_or_false'),
            'skills.array'                          => __('responses.skills_array'),
            'skills.*.numeric'                      => __('responses.skills_numeric'),
            'skills.*.exists'                       => __('responses.skill_not_exists'),
            'skills.required'                       => __('responses.skills_required'),
            'jobs.array'                            => __('responses.jobs_array'),
            'jobs.*.numeric'                        => __('responses.jobs_numeric'),
            'jobs.*.exists'                         => __('responses.job_not_exists'),
            'jobs.required'                         => __('responses.jobs_required'),
            'resource_modules.boolean'              => __('responses.true_or_false'),
            'resource_module_openai.boolean'        => __('responses.true_or_false'),
            'openai_resource_module_types.array'    => __('responses.openai_resource_module_types_array'),
            'openai_resource_module_types.*'        => __('responses.openai_resource_module_types_incorrect'),
            'resource_module_go1.boolean'           => __('responses.true_or_false'),
            'go1_resource_module_types.array'       => __('responses.go1_resource_module_types_array'),
            'go1_resource_module_types.*'           => __('responses.go1_resource_module_types_incorrect'),
            'resource_module_prepr.boolean'         => __('responses.true_or_false'),
        ];
    }
}
