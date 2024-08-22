<?php

namespace App\Http\Requests\Manage\ChallengePath;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateChallengePathRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $achievement_en_switch = $this->request->get('is_achievement_enabled');
        $base_rules = [
            'status'                  => 'required|in:draft,publish,archive',
            'title'                   => 'required|max:255|unique:challenge_paths,title',
            'description'             => 'required',
            'category_id'             =>  Rule::exists('categories', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'level_id'                => 'required|'.Rule::exists('levels', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'duration_id'             => 'required|'.Rule::exists('durations', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
             }),
            'challenge_ids'           => 'required|array',
            'challenge_ids.*'         =>  Rule::exists('challenges', 'uuid')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'is_sequential'           => 'in:yes,no',
            'privacy'                 => 'in:yes,no',
            'is_achievement_enabled'  => 'in:yes,no',
            'is_auto_created'         => 'in:yes,no',
            'skills'                  => 'required|array',
            'skills.*'                => 'numeric|'.Rule::exists('skills', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'skill_groups'            => 'nullable|array',
            'skill_groups.*'          => 'numeric|'.Rule::exists('skill_groups', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'skill_stacks'            => 'nullable|array',
            'skill_stacks.*'          => 'numeric|'.Rule::exists('skill_stacks', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'tags'                    => 'required|array',
            'tags.*'                  => 'numeric|'.Rule::exists('tags', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            'tag_groups'              => 'nullable|array',
            'tag_groups.*'            => 'numeric|'.Rule::exists('tag_groups', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),

        ];
        if ($achievement_en_switch == 'Yes' || $achievement_en_switch == 'yes') {
            $base_rules['achievement_name'] = 'required';
            $base_rules['achievement_points'] = 'required';
            $base_rules['achievement_image'] = 'required|mimes:jpeg,jpg,png,webp|max:1024';
        }

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

    public function messages()
    {
        return [
            'title.required'                 => __('responses.title_required'),
            'title.unique'                   => __('responses.challenge_path_title_unique'),
            'description.required'           => __('responses.description_required'),
            'category_id.required'           => __('responses.category_id_required'),
            'category_id.exists'             => __('responses.category_not_found'),
            'level_id.required'              => __('responses.level_id_required'),
            'level_id.exists'                => __('responses.level_id_exists'),
            'duration_id.required'           => __('responses.duration_id_required'),
            'duration_id.exists'             => __('responses.duration_id_exists'),
            'challenge_ids.required'         => __('responses.challenge_id_required'),
            'challenge_ids.exists'           => __('responses.challenge_id_exists'),
            'challenge_ids.array'            => __('responses.challenge_id_array'),
            'is_sequential.in'               => __('responses.choose_yes_no'),
            'is_achievement_enabled.in'      => __('responses.choose_yes_no'),
            'privacy.in'                     => __('responses.choose_yes_no'),
            'achievement_name.required'      => __('responses.achievement_name_required'),
            'achievement_points.required'    => __('responses.achievement_points_required'),
            'achievement_image.required'     => __('responses.achievement_image_required'),
            'achievement_image.mimes'        => __('responses.mimes_image'),
            'achievement_image.max'          => __('responses.mimes_image_max'),
            'skills.required'                => __('responses.skills_required'),
            'skills.required_if'             => __('responses.skill_not_found'),
            'skill_groups.*.exists'          => __('responses.skill_groups_not_exists'),
            'skill_groups.*.array'           => __('responses.skill_groups_array'),
            'skill_stacks.*.array'           => __('responses.skill_stacks_array'),
            'skill_stacks.*.exists'          => __('responses.skill_stack_not_found'),
            'tags.required'                  => __('responses.tags_required'),
            'tags.numeric'                   => __('responses.tags_numeric'),
            'tag_groups.*.exists'            => __('responses.tag_groups_not_found'),
            'tag_groups.*.array'             => __('responses.tag_groups_array'),
            'tag_groups.*.numeric'           => __('responses.tag_groups_numeric'),
        ];
    }
}
