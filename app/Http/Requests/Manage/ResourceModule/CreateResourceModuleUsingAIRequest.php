<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateResourceModuleUsingAIRequest extends FormRequest
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
            'title'                          => 'required|unique:resource_modules,title',
            'organization_id'                => 'required|exists:organizations,uuid',
            'description'                    => 'required',
            'duration_id'                    => 'required|exists:durations,id',
            'level_id'                       => 'required|exists:levels,id',
            'skills'                         => 'required|array',
            'skills.*'                       => 'numeric|exists:skills,id',
            'is_ai_created'                  => 'required|boolean',
            'skill_titles'                   => 'nullable|array',
            'level'                          => 'nullable',
            'duration'                       => 'nullable',
            'resource_modules'               => 'required|array',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'title.required'                 => __('responses.title_required'),
            'title.unique'                   => __('responses.title_unique'),
            'description.required'           => __('responses.description_required'),
            'organization_id.required'       => __('responses.organization_id_required'),
            'organization_id.exists'         => __('responses.organization_not_exists'),
            'skills.array'                   => __('responses.skills_array'),
            'skills.*.numeric'               => __('responses.skills_numeric'),
            'skills.*.exists'                => __('responses.skill_not_exists'),
            'skills.required'                => __('responses.skills_required'),
            'duration_id.required'           => __('responses.duration_id_required'),
            'duration_id.exists'             => __('responses.duration_id_exists'),
            'level_id.required'              => __('responses.level_id_required'),
            'level_id.exists'                => __('responses.level_id_exists'),
            'is_ai_created'                  => __('responses.choose_yes_no'),
            'skill_titles'                   => __('responses.skill_titles_array'),
            'resource_modules.required'      => __('responses.resource_modules_required'),
            'resource_modules.array'         => __('responses.resource_modules_array'),
        ];
    }
}
