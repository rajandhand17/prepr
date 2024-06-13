<?php

namespace App\Http\Requests\Manage\ResourceCollection;

use App\Services\Manage\ResourceCollectionService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use League\Container\Exception\NotFoundException;

class UpdateResourceCollectionRequest extends FormRequest
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
        $resourceCollection = ResourceCollectionService::getResourceCollectionBasedOnSlug(request()->route('slug'));
        if (!$resourceCollection) {
            throw new NotFoundException();
        }
        $base_rules = [
            'title'                  => 'required|max:255|unique:resource_collections,title,'.$resourceCollection->id,
            'description'            => 'required',
            'cover_image'            => 'nullable|mimes:jpeg,jpg,png,webp|max:1024',
            'privacy'                => 'required|in:yes,no',
            'status'                 => 'required|in:draft,publish,archive',
            'lab_ids'                => 'required|array',
            'lab_ids.*'              => 'exists:labs,uuid',
            'challenge_ids'          => 'required|array',
            'challenge_ids.*'        => 'exists:challenges,uuid',
            'resource_ids'           => 'required|array',
            'resource_ids.*'         => 'exists:resource_modules,uuid',
            'skills'                 => 'required|array',
            'skills.*'               => 'numeric|exists:skills,id',
            'tags'                   => 'required|array',
            'tags.*'                 => 'numeric|exists:tags,id',
            'tag_groups'             => 'array',
            'level'                  => 'required|exists:levels,id',
            'duration'               => 'required|exists:durations,id',
            'tag_groups.*'           => 'numeric|exists:tag_groups,id',
            'skill_groups'           => 'array',
            'skill_groups.*'         => 'numeric|exists:skill_groups,id',
            'skill_stacks'           => 'array',
            'skill_stacks.*'         => 'numeric|exists:skill_stacks,id',
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

    public function messages()
    {
        return [
            'title.required'                 => __('responses.title_required'),
            'title.unique'                   => __('responses.title_unique'),
            'title.max'                      => __('responses.title_max'),
            'description.required'           => __('responses.description_required'),
            'privacy.required'               => __('responses.privacy_required'),
            'privacy.in'                     => __('responses.choose_yes_no'),
            'status.required'                => __('responses.status_required'),
            'status.in'                      => __('responses.status_in'),
            'is_global.required'             => __('responses.choose_yes_no'),
            'cover_image.mimes'              => __('responses.cover_image_type'),
            'cover_image.max'                => __('responses.cover_image_max'),
            'skills.array'                   => __('responses.skills_array'),
            'skills.*.numeric'               => __('responses.skills_numeric'),
            'skills.*.exists'                => __('responses.skill_not_exists'),
            'tags.array'                     => __('responses.tags_array'),
            'tags.*.numeric'                 => __('responses.tags_numeric'),
            'tags.*.exists'                  => __('responses.tag_not_exists'),
            'tag_groups.array'               => __('responses.tag_groups_array'),
            'tag_groups.*.numeric'           => __('responses.tag_groups_numeric'),
            'tag_groups.*.exists'            => __('responses.tag_group_not_exists'),
            'skill_groups.array'             => __('responses.skill_groups_array'),
            'skill_groups.*.numeric'         => __('responses.skill_groups_numeric'),
            'skill_groups.*.exists'          => __('responses.skill_groups_not_exists'),
            'skill_stacks.array'             => __('responses.skill_stacks_array'),
            'skill_stacks.*.numeric'         => __('responses.skill_stacks_numeric'),
            'skill_stacks.*.exists'          => __('responses.skill_stacks_not_exists'),
            'tags.required'                  => __('responses.tags_required'),
            'skills.required'                => __('responses.skills_required'),
            'lab_ids.required'               => __('responses.lab_field_required'),
            'lab_ids.array'                  => __('responses.lab_field_array'),
            'lab_ids.*.exists'               => __('responses.lab_field_invalid_id'),
            'challenge_ids.required'         => __('responses.challenge_field_required'),
            'challenge_ids.array'            => __('responses.challenge_field_array'),
            'challenge_ids.*.exists'         => __('responses.challenge_field_invalid_id'),
            'level.required'                 => __('responses.level_id_required'),
            'level.exists'                   => __('responses.level_id_exists'),
            'duration.required'              => __('responses.duration_id_required'),
            'duration.exists'                => __('responses.duration_id_exists'),
        ];
    }
}
