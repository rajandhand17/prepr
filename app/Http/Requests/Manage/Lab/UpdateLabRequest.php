<?php

namespace App\Http\Requests\Manage\Lab;

use App\Services\Manage\LabProgramService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use League\Container\Exception\NotFoundException;

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
        $lab = LabProgramService::getLabBasedOnSlug(request()->route('slug'));
        if (!$lab) {
            throw new NotFoundException();
        }
        $achievement_en_switch = $this->request->get('is_achievement_enabled');

        $base_rules = [
            'cover_image'    => 'image|mimes:jpeg,jpg,png,webp|max:1024',
            'title'          => 'required|max:255|unique:labs,title,'.$lab->id,
            'request_type'   => 'required|in:draft,publish,archive',
            'type'           => 'required|in:assess,onboard,engage,grow,na',
            'description'    => 'required_if:request_type,publish|nullable',
            'organization_id'=> 'required|exists:organizations,uuid',
            'category_id'    => 'required|exists:categories,id',
            'duration_id'    => 'required|exists:durations,id',
            'level_id'       => 'required|exists:levels,id',
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
            'request_type.required'          => __('responses.request_type_required'),
            'request_type.in'                => __('responses.request_type_status'),
            'privacy.in'                     => __('responses.choose_yes_no'),
            'privacy.required_if'            => __('responses.privacy_required'),
            'latitude.required_if'           => __('responses.latitude_required'),
            'longitude.required_if'          => __('responses.longitude_required'),
            'organization_id.required'       => __('responses.organization_id_required'),
            'organization_id.exists'         => __('responses.organization_not_found'),
            'title.required_if'              => __('responses.title_required'),
            'title.unique'                   => __('responses.lab_title_unique'),
            'description.required_if'        => __('responses.description_required'),
            'country.required_if'            => __('responses.country_required'),
            'city.required_if'               => __('responses.city_required'),
            'organizartion_id.required'      => __('responses.organization_id_required'),
            'location.required_if'           => __('responses.location_required'),
            'category_id.required'           => __('responses.category_id_required'),
            'category_id.exists'             => __('responses.category_not_found'),
            'skills.required'                => __('responses.skills_required'),
            'skills.required_if'             => __('responses.skill_not_found'),
            'tags.required'                  => __('responses.tags_required'),
            'tags.numeric'                   => __('responses.tags_numeric'),
            'achievement_name.required'      => __('responses.achievement_name_required'),
            'achievement_points.required'    => __('responses.achievement_points_required'),
            'achievement_image.required'     => __('responses.achievement_image_required'),
            'achievement_conditions.required'=> __('responses.achievement_conditions_required'),
            'achievement_conditions.array'   => __('responses.achievement_conditions_array'),
            'challenge_id.required'          => __('responses.challenge_id_required'),
            'challenge_path_id.required'     => __('responses.challenge_path_id_required'),
            'skill_groups.*.exists'          => __('responses.skill_groups_not_exists'),
            'skill_groups.*.array'           => __('responses.skill_groups_array'),
            'skill_stacks.*.array'           => __('responses.skill_stacks_array'),
            'skill_stacks.*.exists'          => __('responses.skill_stack_not_found'),
            'tag_groups.*.exists'            => __('responses.tag_groups_not_found'),
            'tag_groups.*.array'             => __('responses.tag_groups_array'),
            'tag_groups.*.numeric'           => __('responses.tag_groups_numeric'),
            'is_notification_enabled.in'     => __('responses.choose_yes_no'),
            'is_achievement_enabled.in'      => __('responses.choose_yes_no'),
            'is_sequential.in'               => __('responses.choose_yes_no'),
            'is_resource_sequential.in'      => __('responses.choose_yes_no'),
            'external_links.array'           => __('responses.external_links_array'),
            'external_links.url'             => __('responses.external_links_valid_url_pattern'),
            'external_link_ids.exists'       => __('responses.external_link_ids_not_exists'),
            'external_link_ids.array'        => __('responses.external_link_ids_array'),
            'external_link_ids.numeric'      => __('responses.external_link_ids_numeric'),
            'lab_programs.*.numeric'         => __('responses.lab_programs_numeric'),
            'lab_programs.*.array'           => __('responses.lab_programs_array'),
            'challenges.*.numeric'           => __('responses.challenges_numeric'),
            'challenges.*.array'             => __('responses.challenges_array'),
            'challenge_paths.*.numeric'      => __('responses.challenge_paths_numeric'),
            'challenge_paths.*.array'        => __('responses.challenge_paths_array'),
            'resource_modules.*.numeric'     => __('responses.resource_modules_numeric'),
            'resource_modules.*.array'       => __('responses.resource_modules_array'),
            'resource_groups.*.numeric'      => __('responses.resource_groups_numeric'),
            'resource_groups.*.array'        => __('responses.resource_groups_array'),
            'resource_collections.*.numeric' => __('responses.resource_collections_numeric'),
            'resource_collections.*.array'   => __('responses.resource_collections_array'),
            'subject_line.max'               => __('responses.subject_line_max'),
            'email_body.max'                 => __('responses.email_body_max'),
            'auto_invite.in'                 => __('responses.choose_yes_no'),
            'invite_email.required'          => __('responses.invite_email_required'),
            'invite_email.csv'               => __('responses.choose_csv_file'),
            'duration_id.required'           => __('responses.duration_id_required'),
            'duration_id.exists'             => __('responses.duration_id_exists'),
            'level_id.required'              => __('responses.level_id_required'),
            'level_id.exists'                => __('responses.level_id_exists'),

        ];
    }
}
