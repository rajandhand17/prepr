<?php

namespace App\Http\Requests\Manage\ResourceCollection;

use App\Services\Manage\ResourceCollectionService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
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
            'type'                   => 'array',
            'type.*'                 => 'nullable|in:assess,onboard,engage,grow',
            'mode'                   => 'array',
            'mode.*'                 => 'nullable|in:team,individual',
            'media_type'             => 'in:image,embedded',
            'privacy'                => 'required|in:public,private',
            'status'                 => 'required|in:draft,publish,archive',
            'lab_ids'                => 'required|array',
            'lab_ids.*'              => Rule::exists('labs', 'uuid')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'challenge_ids'          => 'required|array',
            'challenge_ids.*'        => Rule::exists('challenges', 'uuid')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'resource_ids'           => 'required|array',
            'resource_ids.*'         => Rule::exists('resource_modules', 'uuid')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'skills'                 => 'required|array',
            'skills.*'               => 'numeric|'.Rule::exists('skills', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'level'                  => 'required|'.Rule::exists('levels', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'duration'               => 'required|'.Rule::exists('durations', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'skill_groups'           => 'array',
            'skill_groups.*'         => 'numeric|'.Rule::exists('skill_groups', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
            'skill_stacks'           => 'array',
            'skill_stacks.*'         => 'numeric|'.Rule::exists('skill_stacks', 'id')->where(function ($query) {
                $query->whereNull('deleted_at');
            }),
        ];
        if ($this->has('media_type') && $this->input('media_type') == 'image') {
            $base_rules['cover_image'] = [
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
                'required',
            ];
        }
        if ($this->has('cover_image')) {
            $base_rules['media_type'] = [
                'required',
            ];
        }
        if ($this->has('cover_image') && $this->input('cover_image') == 'embedded') {
            $regexYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexNoCookieYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube-nocookie.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexVimeo = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/player.vimeo.com\/(?:\b|_).*?(?:\b|_)iframe>/';

            $cover_embedded = $this->input('cover_image');
            $isValid = 0;

            // Check for YouTube iframe
            preg_match_all($regexYoutube, $cover_embedded, $matchesYoutube);
            $isValid += count($matchesYoutube[0]);

            // Check for YouTube no-cookie iframe
            preg_match_all($regexNoCookieYoutube, $cover_embedded, $matchesNoCookieYoutube);
            $isValid += count($matchesNoCookieYoutube[0]);

            // Check for Vimeo iframe
            preg_match_all($regexVimeo, $cover_embedded, $matchesVimeo);
            $isValid += count($matchesVimeo[0]);

            $base_rules['cover_image'] = [
                'required',
                function ($attribute, $value, $fail) use ($isValid) {
                    if ($isValid === 0) {
                        $fail($attribute.' must contain exactly one valid YouTube or Vimeo iframe.');
                    } elseif ($isValid > 1) {
                        $fail($attribute.' must not contain more than one valid YouTube or Vimeo iframe.');
                    }
                },
            ];
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
            'title.unique'                   => __('responses.title_unique'),
            'title.max'                      => __('responses.title_max'),
            'description.required'           => __('responses.description_required'),
            'privacy.required'               => __('responses.privacy_required'),
            'privacy.in'                     => __('responses.public_or_private'),
            'status.required'                => __('responses.status_required'),
            'status.in'                      => __('responses.status_in'),
            'is_global.required'             => __('responses.choose_yes_no'),
            'cover_image.mimes'              => __('responses.cover_image_type'),
            'cover_image.max'                => __('responses.cover_image_max'),
            'skills.array'                   => __('responses.skills_array'),
            'skills.*.numeric'               => __('responses.skills_numeric'),
            'skills.*.exists'                => __('responses.skill_not_exists'),
            'skill_groups.array'             => __('responses.skill_groups_array'),
            'skill_groups.*.numeric'         => __('responses.skill_groups_numeric'),
            'skill_groups.*.exists'          => __('responses.skill_groups_not_exists'),
            'skill_stacks.array'             => __('responses.skill_stacks_array'),
            'skill_stacks.*.numeric'         => __('responses.skill_stacks_numeric'),
            'skill_stacks.*.exists'          => __('responses.skill_stacks_not_exists'),
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
            'type.array'                     => __('responses.type_array'),
            'type.*.in'                      => __('responses.resource_type_in'),
            'mode.array'                     => __('responses.mode_array'),
            'mode.*.in'                      => __('responses.resource_mode_in'),
            'media_type.in'                  => __('responses.choose_image_embedded'),
        ];
    }
}
