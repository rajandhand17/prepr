<?php

namespace App\Http\Requests\Manage\Challenge;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class createChallengeUsingAIRequest extends FormRequest
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
            'steps'                                 => 'required|array',
            'reflections'                           => 'required|array',
            'is_ai_created'                         => 'in:yes,no',
            'resource_modules'                      => 'nullable',
            'resource_module_prepr'                 => 'nullable',
            'resource_module_openai'                => 'nullable',
            'resource_module_go1'                   => 'nullable',
            'openai_resource_module_types'          => 'nullable',
            'go1_resource_module_types'             => 'nullable',
            'agreement'                             => 'required',
            'achievement_name'                      => 'required',
            'achievement_prize'                     => 'required',
            'achievement_points'                    => 'required|numeric',
            'timeline_type'                         => 'required',
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
            'skills.required'                             => __('responses.skills_required'),
            'skills.required_if'                          => __('responses.skill_not_found'),
            'steps.required'                              => __('responses.steps_missing'),
            'reflections.required'                        => __('responses.reflections_missing'),
            'is_ai_created'                               => __('responses.choose_yes_no'),
            'agreement.required'                          => __('responses.agreement_missing'),
            'achievement_name.required'                   => __('responses.achievement_name_missing'),
            'achievement_points.required'                 => __('responses.achievement_points_missing'),
            'achievement_prize.required'                  => __('responses.achievement_prize_missing'),
            'timeline_type'                               => __('responses.timeline_type_missing')
        ];
    }
}
