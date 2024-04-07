<?php

namespace App\Http\Requests\Manage\Challenge;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'title'                                 => 'required_if:request_type,publish|unique:challenges,title',
            'description'                           => 'required_if:request_type,publish',
            'category_id'                           => 'required|exists:categories,id',
            'organization_id'                       => 'required|exists:organizations,uuid',
            'duration_id'                           => 'required|exists:durations,id',
            'level_id'                              => 'required|exists:levels,id',
            'skills'                                => 'required|array',
            'skills.*'                              => 'numeric|exists:skills,id',
            'jobs'                                  => 'required|array',
            'jobs.*'                                => 'numeric|exists:job_titles,id',
            'steps'                                 => 'required|array',
            'reflections'                           => 'required|array',
            'is_ai_created'                         => 'required|boolean',
            'agreement'                             => 'required',
            'achievement_name'                      => 'required',
            'achievement_prize'                     => 'required',
            'achievement_points'                    => 'required|numeric',
            'timeline_type'                         => 'required|in:flexible,restricted',
            'resource_modules'                      => 'nullable|array',
            'resource_module_openai'                => 'nullable|boolean',
            'openai_resource_module_types'          => 'nullable|array',
            'resource_module_go1'                   => 'nullable|boolean',
            'go1_resource_module_types'             => 'nullable|array',
            'resource_module_prepr'                 => 'nullable|boolean',
            'skill_titles'                          => 'nullable|array',
            'job_titles'                            => 'nullable|array',
            'level'                                 => 'nullable',
            'duration'                              => 'nullable',
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
            'title.required_if'                           => __('responses.title_required'),
            'title.unique'                                => __('responses.challenge_title_unique'),
            'description.required_if'                     => __('responses.description_required'),
            'organization_id.required'                    => __('responses.organization_id_required'),
            'organization_id.exists'                      => __('responses.organization_not_found'),
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
            'jobs.array'                                  => __('responses.jobs_array'),
            'jobs.*.numeric'                              => __('responses.jobs_numeric'),
            'jobs.*.exists'                               => __('responses.job_not_exists'),
            'jobs.required'                               => __('responses.jobs_required'),
            'steps.required'                              => __('responses.steps_required'),
            'steps.array'                                 => __('responses.steps_array'),
            'reflections.required'                        => __('responses.reflections_required'),
            'reflections.array'                           => __('responses.reflections_array'),
            'is_ai_created'                               => __('responses.true_or_false'),
            'agreement.required'                          => __('responses.agreement_required'),
            'achievement_name.required'                   => __('responses.achievement_name_required'),
            'achievement_prize.required'                  => __('responses.achievement_prize_required'),
            'achievement_points.required'                 => __('responses.achievement_points_required'),
            'achievement_points.numeric'                  => __('responses.achievement_points_numeric_allowed_only'),
            'timeline_type'                               => __('responses.timeline_type_required'),
            'resource_modules.array'                      => __('responses.resource_modules_array'),
            'resource_module_openai'                      => __('responses.true_or_false'),
            'openai_resource_module_types'                => __('responses.openai_resource_module_types_array'),
            'resource_module_go1'                         => __('responses.true_or_false'),
            'go1_resource_module_types'                   => __('responses.go1_resource_module_types_array'),
            'resource_module_prepr'                       => __('responses.true_or_false'),
            'skill_titles'                                => __('responses.skill_titles_array'),
        ];
    }
}
