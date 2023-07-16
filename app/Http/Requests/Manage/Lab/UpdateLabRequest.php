<?php

namespace App\Http\Requests\Manage\Lab;

use App\Services\Manage\LabService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLabRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
        $lab = LabService::checkSlug(request()->route('slug'));
        $achievement_en_switch = $this->request->get('is_achievement_enabled');
        if ($lab) {
            $base_rules = [
                'cover_image' => 'image|mimes:jpeg,jpg,png,webp|max:1024',
                'title'       => 'required|max:255|unique:labs,title,'.$lab->id,
            ];
        } else {
            $base_rules = [
                'cover_image' => 'image|mimes:jpeg,jpg,png,webp|max:1024',
                'title'       => 'required|max:255|unique:labs,title,',
            ];
        }
        $base_rules = [
            'request_type'   => 'required|in:draft,publish,archive',
            'description'    => 'required_if:request_type,publish|nullable',
            'organization_id'=> 'required|exists:organizations,id',
            'category_id'    => 'required|exists:categories,id',
            'privacy'        => 'required_if:request_type,publish|in:yes,no',
            'location'       => 'required_if:request_type,publish|nullable',
            'latitude'       => 'required_if:request_type,publish|nullable',
            'longitude'      => 'required_if:request_type,publish|nullable',
            'country'        => 'required_if:request_type,publish|nullable',
            'city'           => 'required_if:request_type,publish|nullable',
            'skills'         => 'required_if:request_type,publish|nullable|array',
            'skills.*'       => 'numeric',
            'skill_groups'   => 'nullable|array',
            'skill_groups.*' => 'numeric',
            'skill_stacks'   => 'nullable|array',
            'skill_stacks.*' => 'numeric',
            'tags'           => 'required_if:request_type,publish|nullable|array',
            'tags.*'         => 'numeric',
            'tag_groups'     => 'nullable|array',
            'tag_groups.*'   => 'numeric',

            'is_notification_enabled'=> 'in:yes,no',

            'is_achievement_enabled'=> 'in:yes,no',

            'is_sequential'=> 'in:yes,no',

            'is_resource_sequential'=> 'in:yes,no',
        ];

        if ($this->request->has('external_links')) {
            $base_rules['external_links'] = 'array';
            $base_rules['external_link_ids'] = 'array|exists:social_links,id';
            $base_rules['external_links.*'] = 'url';
            $base_rules['external_link_ids.*'] = 'numeric';
        }

        if ($achievement_en_switch == 'yes') {
            $base_rules['achievement_name'] = 'required';
            $base_rules['achievement_points'] = 'required';
            $base_rules['achievement_conditions'] = 'required|array';
            $base_rules['achievement_image'] = 'required';
        }

        if ($this->request->has('lab_programs')) {
            $base_rules['lab_programs'] = 'array';
            $base_rules['lab_programs.*'] = 'numeric';
        }

        if ($this->request->has('challenges')) {
            $base_rules['challenges'] = 'array';
            $base_rules['challenges.*'] = 'numeric';
        }

        if ($this->request->has('challenge_paths')) {
            $base_rules['challenge_paths'] = 'array';
            $base_rules['challenge_paths.*'] = 'numeric';
        }

        if ($this->request->has('resource_modules')) {
            $base_rules['resource_modules'] = 'array';
            $base_rules['resource_modules.*'] = 'numeric';
        }

        if ($this->request->has('resource_groups')) {
            $base_rules['resource_groups'] = 'array';
            $base_rules['resource_groups.*'] = 'numeric';
        }

        if ($this->request->has('resource_collections')) {
            $base_rules['resource_collections'] = 'array';
            $base_rules['resource_collections.*'] = 'numeric';
        }

        if ($this->request->has('invite_type')) {
            $check_invite_type = $this->request->get('invite_type');
            $base_rules['subject_line'] = 'max:250';
            $base_rules['email_body'] = 'max:2000';
            $base_rules['auto_invite'] = 'required|in:yes,no,na';

            if ($check_invite_type == 'csv') {
                $base_rules['invite_email'] = 'required|mimes:csv,txt';
            }
            if ($check_invite_type == 'email') {
                $base_rules['invite_email'] = 'required|array';
                $base_rules['invite_email.*'] = 'required|email';
            }
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

            'request_type.required'          => __('responses.required_field'),
            'request_type.in'                => __('responses.choose_draft_publish_archive'),
            'privacy.in'                     => __('responses.choose_yes_no'),
            'privacy.required_if'            => __('responses.required_field'),
            'latitude.required_if'           => __('responses.required_field'),
            'longitude.required_if'          => __('responses.required_field'),
            'organization_id.required'       => __('responses.required_field'),
            'organization_id.exists'         => __('responses.organization_not_found'),
            'title.required_if'              => __('responses.required_field'),
            'title.unique'                   => __('responses.lab_title_unique'),
            'description.required_if'        => __('responses.required_field'),
            'country.required_if'            => __('responses.required_field'),
            'city.required_if'               => __('responses.required_field'),
            'organizartion_id.required'      => __('responses.required_field'),
            'location.required_if'           => __('responses.required_field'),
            'category_id.required'           => __('responses.required_field'),
            'category_id.exists'             => __('responses.category_not_found'),
            'skills.required'                => __('responses.required_field'),
            'skills.required_if'             => __('responses.skill_not_found'),
            'tags.required'                  => __('responses.required_field'),
            'tags.numeric'                   => __('responses.numeric_data_allowed'),
            'achievement_name.required'      => __('responses.required_field'),
            'achievement_points.required'    => __('responses.required_field'),
            'achievement_image.required'     => __('responses.required_field'),
            'achievement_conditions.required'=> __('responses.required_field'),
            'achievement_conditions.array'   => __('responses.array'),
            'challenge_id.required'          => __('responses.required_field'),
            'challenge_path_id.required'     => __('responses.required_field'),
            'skill_groups.*.exists'          => __('responses.not_exists'),
            'skill_groups.*.array'           => __('responses.array'),
            'skill_stacks.*.array'           => __('responses.array'),
            'skill_stacks.*.exists'          => __('responses.skill_stack_not_found'),
            'tag_groups.*.exists'            => __('responses.tag_groups_not_found'),
            'tag_groups.*.array'             => __('responses.array'),
            'tag_groups.*.numeric'           => __('responses.numeric_data_allowed'),
            'is_notification_enabled.in'     => __('responses.choose_yes_no'),
            'is_achievement_enabled.in'      => __('responses.choose_yes_no'),
            'is_sequential.in'               => __('responses.choose_yes_no'),
            'is_resource_sequential.in'      => __('responses.choose_yes_no'),
            'external_links.array'           => __('responses.array'),
            'external_links.url'             => __('responses.valid_url_pattern'),
            'external_link_ids.exists'       => __('responses.not_exists'),
            'external_link_ids.array'        => __('responses.array'),
            'external_link_ids.numeric'      => __('responses.numeric_data_allowed'),
            'lab_programs.*.numeric'         => __('responses.numeric_data_allowed'),
            'lab_programs.*.array'           => __('responses.array'),
            'challenges.*.numeric'           => __('responses.numeric_data_allowed'),
            'challenges.*.array'             => __('responses.array'),
            'challenge_paths.*.numeric'      => __('responses.numeric_data_allowed'),
            'challenge_paths.*.array'        => __('responses.array'),
            'resource_modules.*.numeric'     => __('responses.numeric_data_allowed'),
            'resource_modules.*.array'       => __('responses.array'),
            'resource_groups.*.numeric'      => __('responses.numeric_data_allowed'),
            'resource_groups.*.array'        => __('responses.array'),
            'resource_collections.*.numeric' => __('responses.numeric_data_allowed'),
            'resource_collections.*.array'   => __('responses.array'),
            'subject_line.max'               => __('responses.max_content_250'),
            'email_body.max'                 => __('responses.max_content_2000'),
            'auto_invite.in'                 => __('responses.choose_yes_no'),
            'invite_email.required'          => __('responses.required_field'),
            'invite_email.csv'               => __('responses.choose_csv_file'),
            'invite_email.*.required'        => __('responses.required_field'),

        ];
    }
}
