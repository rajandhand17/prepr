<?php

namespace App\Http\Requests\Manage\ResourceModule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'title'                          => 'required',
            'description'                    => 'required',
            'duration_id'                    => 'required|'.Rule::exists('durations', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'level_id'                       => 'required|'.Rule::exists('levels', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'skills'                         => 'required|array',
            'skills.*'                       => 'numeric|'.Rule::exists('skills', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'is_ai_created'                  => 'required|boolean',
            'skill_titles'                   => 'nullable|array',
            'level'                          => 'nullable',
            'duration'                       => 'nullable',
            'resource_module_items'          => 'required|array',
        ];

        return $base_rules;
    }

    public function messages()
    {
        return [
            'title.required'                        => __('responses.title_required'),
            'title.unique'                          => __('responses.title_unique'),
            'description.required'                  => __('responses.description_required'),
            'skills.array'                          => __('responses.skills_array'),
            'skills.*.numeric'                      => __('responses.skills_numeric'),
            'skills.*.exists'                       => __('responses.skill_not_exists'),
            'skills.required'                       => __('responses.skills_required'),
            'duration_id.required'                  => __('responses.duration_id_required'),
            'duration_id.exists'                    => __('responses.duration_id_exists'),
            'level_id.required'                     => __('responses.level_id_required'),
            'level_id.exists'                       => __('responses.level_id_exists'),
            'is_ai_created.required'                => __('responses.is_ai_created_required'),
            'is_ai_created.boolean'                 => __('responses.choose_yes_no'),
            'skill_titles.array'                    => __('responses.skill_titles_array'),
            'resource_module_items.required'        => __('responses.resource_module_items_required'),
            'resource_module_items.array'           => __('responses.resource_module_items_array'),
        ];
    }
}
