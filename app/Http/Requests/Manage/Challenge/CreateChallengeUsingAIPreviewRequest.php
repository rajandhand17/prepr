<?php

namespace App\Http\Requests\Manage\Challenge;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class createChallengeUsingAIPreviewRequest extends FormRequest
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
            'organization_id'                       => 'required|exists:organizations,uuid',
            'duration_id'                           => 'required|exists:durations,id',
            'level_id'                              => 'required|exists:levels,id',
            'additional_information'                => 'nullable',
            'resource_modules'                      => 'nullable',
            'is_ai_created'                         => 'in:yes,no',
            'skills'                                => 'required|array',
            'jobs'                                  => 'required|array',
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
            'organization_id.required'                    => __('responses.organization_id_required'),
            'organization_id.exists'                      => __('responses.organization_not_found'),
            'duration_id.required'                        => __('responses.duration_id_required'),
            'duration_id.exists'                          => __('responses.duration_id_exists'),
            'level_id.required'                           => __('responses.level_id_required'),
            'level_id.exists'                             => __('responses.level_id_exists'),
            'is_ai_created'                               => __('responses.choose_yes_no'),
            'skills.required'                             => __('responses.skills_required'),
            'skills.required_if'                          => __('responses.skill_not_found'),
            'jobs.required'                               => __('responses.jobs_required'),
            'jobs.required_if'                            => __('responses.job_not_found'),
        ];
    }
}
