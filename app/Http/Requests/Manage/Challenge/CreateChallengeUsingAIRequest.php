<?php

namespace App\Http\Requests\Manage\Challenge;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateChallengeUsingAIRequest extends FormRequest
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
            'challengeTitle'                        => 'required',
            'challengeDescription'                  => 'required',
            'category_id'                           => 'required|'.Rule::exists('categories', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'duration_id'                           => 'required|'.Rule::exists('durations', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'duration'                              => 'nullable',
            'level_id'                              => 'required|'.Rule::exists('levels', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'level'                                 => 'nullable',
            'skills'                                => 'required|array',
            'skills.*'                              => 'numeric|'.Rule::exists('skills', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'skill_titles'                          => 'nullable|array',
            'jobs'                                  => 'nullable|array',
            'jobs.*'                                => 'numeric|'.Rule::exists('job_titles', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'job_titles'                            => 'nullable|array',
            'steps'                                 => 'required|array',
            'reflections'                           => 'required|array',
            'is_ai_created'                         => 'required|boolean',
            'resource_modules'                      => 'nullable|array',
            'resource_modules.*'                    => 'required_if:resource_modules,exists|'.Rule::exists('resource_modules', 'uuid')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
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
            'challengeTitle.required'                     => __('responses.title_required'),
            'challengeTitle.unique'                       => __('responses.challenge_title_unique'),
            'challengeDescription.required'               => __('responses.description_required'),
            'category_id.required'                        => __('responses.category_id_required'),
            'category_id.exists'                          => __('responses.category_not_found'),
            'duration_id.required'                        => __('responses.duration_id_required'),
            'duration_id.exists'                          => __('responses.duration_id_exists'),
            'level_id.required'                           => __('responses.level_id_required'),
            'level_id.exists'                             => __('responses.level_id_exists'),
            'skills.array'                                => __('responses.skills_array'),
            'skills.*.numeric'                            => __('responses.skills_numeric'),
            'skills.*.exists'                             => __('responses.skill_not_exists'),
            'skills.required'                             => __('responses.skills_required'),
            'skill_titles.array'                          => __('responses.skill_titles_array'),
            'jobs.array'                                  => __('responses.jobs_array'),
            'jobs.*.numeric'                              => __('responses.jobs_numeric'),
            'jobs.*.exists'                               => __('responses.job_not_exists'),
            'jobs.required'                               => __('responses.jobs_required'),
            'job_titles.array'                            => __('responses.job_titles_array'),
            'steps.required'                              => __('responses.steps_required'),
            'steps.array'                                 => __('responses.steps_array'),
            'reflections.required'                        => __('responses.reflections_required'),
            'reflections.array'                           => __('responses.reflections_array'),
            'is_ai_created.required'                      => __('responses.is_ai_created_required'),
            'is_ai_created.boolean'                       => __('responses.true_or_false'),
            'resource_modules.array'                      => __('responses.resource_modules_array'),
            'resource_modules.*.exists'                   => __('responses.resource_ids_array_not_exists'),
            'resource_module_openai.boolean'              => __('responses.true_or_false'),
            'openai_resource_module_types.array'          => __('responses.openai_resource_module_types_array'),
            'openai_resource_module_types.*'              => __('responses.openai_resource_module_types_incorrect'),
            'resource_module_go1.boolean'                 => __('responses.true_or_false'),
            'go1_resource_module_types.array'             => __('responses.go1_resource_module_types_array'),
            'go1_resource_module_types.*'                 => __('responses.go1_resource_module_types_incorrect'),
            'resource_module_prepr.boolean'               => __('responses.true_or_false'),
        ];
    }
}
