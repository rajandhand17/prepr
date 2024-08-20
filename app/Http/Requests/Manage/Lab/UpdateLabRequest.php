<?php

namespace App\Http\Requests\Manage\Lab;

use App\Rules\AirmeetEventUrlRule;
use App\Services\Manage\LabService;
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
        $lab = LabService::getLabBasedOnSlug(request()->route('slug'));
        if (!$lab) {
            throw new NotFoundException();
        }
        $achievement_en_switch = $this->request->get('is_achievement_enabled');

        $base_rules = [
            'title'                    => 'required|max:255|unique:labs,title,'.$lab->id,
            'request_type'             => 'required|in:draft,publish,archive',
            'type'                     => 'required|array',
            'type.*'                   => 'in:assess,onboard,engage,grow',
            'mode.*'                   => 'required|in:team,individual',
            'media_type'               => 'in:image,embedded',
            'description'              => 'required_if:request_type,publish|nullable',
            'category_id'              => 'required|exists:categories,id',
            'duration_id'              => 'required|exists:durations,id',
            'level_id'                 => 'required|exists:levels,id',
            'privacy'                  => 'required_if:request_type,publish|in:yes,no',
            'location'                 => 'required_if:request_type,publish|nullable',
            'latitude'                 => 'required_if:request_type,publish|nullable',
            'longitude'                => 'required_if:request_type,publish|nullable',
            'country'                  => 'required_if:request_type,publish|nullable',
            'city'                     => 'required_if:request_type,publish|nullable',
            'skills'                   => 'required_if:request_type,publish|nullable|array',
            'skills.*'                 => 'numeric',
            'skill_groups'             => 'nullable|array',
            'skill_groups.*'           => 'numeric',
            'skill_stacks'             => 'nullable|array',
            'skill_stacks.*'           => 'numeric',
            'is_notification_enabled'  => 'in:yes,no',
            'is_achievement_enabled'   => 'in:yes,no',
            'is_sequential'            => 'in:yes,no',
            'is_resource_sequential'   => 'in:yes,no',
            'external_links'           => 'array',
            'external_link_ids'        => 'array|exists:social_links,id',
            'external_links.*'         => 'url',
            'external_link_ids.*'      => 'numeric',
            'integrate_campus_connect' => 'in:both,job,story,no',
            'is_live_event_enabled'    => 'nullable|in:yes,no',
        ];

        // Lab cover image validation
        if ($this->has('media_type') && $this->input('media_type') == 'image') {
            if ($lab->media != null && $lab->getRawOriginal('media') == 'default_images/lab.webp') {
                if ($this->hasFile('cover_image') && $this->file('cover_image')->isValid()) {
                    $base_rules['cover_image'] = [
                        'required',
                        'mimes:jpeg,jpg,png,webp',
                        'max:5120',
                        function ($attribute, $value, $fail) {
                            if ($value && $value->isValid()) {
                                $image = getimagesize($value);
                                if ($image && ($image[0] < 625 || $image[1] < 355)) {
                                    $fail(''.$attribute.' must be at least 625x355 pixels.');
                                }
                            }
                        },
                    ];
                }
            }
        }

        if ($this->has('media_type') && $this->input('media_type') == 'embedded') {
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

        /*** CAMPUS CONNECT JOB RULE */
        if (in_array($this->get('integrate_campus_connect'), ['both', 'job'])) {
            $base_rules['campus_connect_job_title'] = 'required|max:140|min:5';
            $base_rules['campus_connect_job_type'] = 'required';
            $base_rules['campus_connect_no_of_position'] = 'required';
            $base_rules['campus_connect_qualification'] = 'required';
            $base_rules['campus_connect_province'] = 'required';
            $base_rules['campus_connect_city'] = 'required';
            $base_rules['campus_connect_description'] = 'required';
            $base_rules['campus_connect_deadline'] = 'required';
            $base_rules['campus_connect_hours_per_week'] = 'required';
            $base_rules['campus_connect_application_instructions'] = 'required';
            $base_rules['campus_connect_preferred_response'] = 'required';
            $base_rules['campus_connect_schools'] = 'required';
            $base_rules['campus_connect_salary_amount'] = 'required|numeric';
            $base_rules['campus_connect_salary_payment_frequency'] = 'required';

            if ($this->get('campus_connect_preferred_response') == 'Via Email') {
                $base_rules['campus_connect_application_email'] = 'required';
            }
        }
        /*** CAMPUS CONNECT JOB RULE END */

        /** CAMPUS CONNECT STORY FORM VALIDATION */
        if (in_array($this->get('integrate_campus_connect'), ['both', 'story'])) {
            $base_rules['campus_connect_story_title'] = 'required|max:140|min:5';
            $base_rules['campus_connect_story_body'] = 'required';
            $base_rules['campus_connect_story_media_type'] = 'nullable';
            $base_rules['campus_connect_story_image_title'] = 'nullable';
            $base_rules['campus_connect_story_image_description'] = 'nullable';
            $base_rules['campus_connect_story_video_youtube_url'] = 'nullable';
            $base_rules['campus_connect_has_image_file'] = 'nullable';
            $base_rules['campus_connect_schools'] = 'required';

            if ($this->get('campus_connect_has_image_file') == 'true' && $this->file('campus_connect_story_image')) {
                $base_rules['campus_connect_story_image'] = 'nullable|file|mimes:jpeg,png,jpg';
            } else {
                $base_rules['campus_connect_story_image'] = 'nullable|string';
            }
        }
        /** CAMPUS CONNECT STORY FORM VALIDATION END */
        if ($achievement_en_switch == 'yes') {
            $base_rules['achievement_name'] = 'required';
            $base_rules['achievement_points'] = 'required';
            $base_rules['achievement_conditions'] = 'required|array';
            $base_rules['achievement_image'] = 'required';
        }

        if ($this->request->has('lab_programs')) {
            $base_rules['lab_programs'] = 'array';
            $base_rules['lab_programs.*'] = 'exists:lab_programs,uuid';
        }

        if ($this->request->has('challenges')) {
            $base_rules['challenges'] = 'array';
            $base_rules['challenges.*'] = 'exists:challenges,uuid';
        }

        if ($this->request->has('challenge_paths')) {
            $base_rules['challenge_paths'] = 'array';
            $base_rules['challenge_paths.*'] = 'exists:challenge_paths,uuid';
        }

        if ($this->request->has('resource_modules')) {
            $base_rules['resource_modules'] = 'array';
            $base_rules['resource_modules.*'] = 'exists:resource_modules,uuid';
        }

        if ($this->request->has('resource_groups')) {
            $base_rules['resource_groups'] = 'array';
            $base_rules['resource_groups.*'] = 'exists:resource_groups,uuid';
        }

        if ($this->request->has('resource_collections')) {
            $base_rules['resource_collections'] = 'array';
            $base_rules['resource_collections.*'] = 'exists:resource_collections,uuid';
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

        $isLiveEventEnabled = $this->get('is_live_event_enabled');
        if ($isLiveEventEnabled === 'yes') {
            $base_rules = [
                ...$base_rules,
                'live_event.url'         => ['required', new AirmeetEventUrlRule()],
                'live_event.is_verified' => ['required', 'in:yes'], // FIRST CHECK FROM AN API TO VERIFY AIRMEET EVENT
            ];
        }

        return $base_rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data'    => $validator->errors(),
        ], 422));
    }

    public function messages()
    {
        return [
            'request_type.required'                            => __('responses.request_type_required'),
            'request_type.in'                                  => __('responses.request_type_status'),
            'type.required'                                    => __('responses.type_required'),
            'type.in'                                          => __('responses.type_in'),
            'mode.required'                                    => __('responses.mode_required'),
            'mode.in'                                          => __('responses.resource_mode_in'),
            'cover_image.mimes'                                => __('responses.cover_image_type'),
            'cover_image.max'                                  => __('responses.cover_image_max'),
            'media_type.in'                                    => __('responses.choose_image_embedded'),
            'privacy.in'                                       => __('responses.choose_yes_no'),
            'privacy.required_if'                              => __('responses.privacy_required'),
            'latitude.required_if'                             => __('responses.latitude_required'),
            'longitude.required_if'                            => __('responses.longitude_required'),
            'title.required_if'                                => __('responses.title_required'),
            'title.unique'                                     => __('responses.lab_title_unique'),
            'description.required_if'                          => __('responses.description_required'),
            'country.required_if'                              => __('responses.country_required'),
            'city.required_if'                                 => __('responses.city_required'),
            'organizartion_id.required'                        => __('responses.organization_id_required'),
            'location.required_if'                             => __('responses.location_required'),
            'category_id.required'                             => __('responses.category_id_required'),
            'category_id.exists'                               => __('responses.category_not_found'),
            'skills.required'                                  => __('responses.skills_required'),
            'skills.required_if'                               => __('responses.skill_not_found'),
            'achievement_name.required'                        => __('responses.achievement_name_required'),
            'achievement_points.required'                      => __('responses.achievement_points_required'),
            'achievement_image.required'                       => __('responses.achievement_image_required'),
            'achievement_conditions.required'                  => __('responses.achievement_conditions_required'),
            'achievement_conditions.array'                     => __('responses.achievement_conditions_array'),
            'challenge_id.required'                            => __('responses.challenge_id_required'),
            'challenge_path_id.required'                       => __('responses.challenge_path_id_required'),
            'skill_groups.*.exists'                            => __('responses.skill_groups_not_exists'),
            'skill_groups.*.array'                             => __('responses.skill_groups_array'),
            'skill_stacks.*.array'                             => __('responses.skill_stacks_array'),
            'skill_stacks.*.exists'                            => __('responses.skill_stack_not_found'),
            'is_notification_enabled.in'                       => __('responses.choose_yes_no'),
            'is_achievement_enabled.in'                        => __('responses.choose_yes_no'),
            'is_sequential.in'                                 => __('responses.choose_yes_no'),
            'is_resource_sequential.in'                        => __('responses.choose_yes_no'),
            'external_links.array'                             => __('responses.external_links_array'),
            'external_links.url'                               => __('responses.external_links_valid_url_pattern'),
            'external_link_ids.exists'                         => __('responses.external_link_ids_not_exists'),
            'external_link_ids.array'                          => __('responses.external_link_ids_array'),
            'external_link_ids.numeric'                        => __('responses.external_link_ids_numeric'),
            'lab_programs.*.numeric'                           => __('responses.lab_programs_numeric'),
            'lab_programs.*.array'                             => __('responses.lab_programs_array'),
            'challenges.*.numeric'                             => __('responses.challenges_numeric'),
            'challenges.*.array'                               => __('responses.challenges_array'),
            'challenge_paths.*.numeric'                        => __('responses.challenge_paths_numeric'),
            'challenge_paths.*.array'                          => __('responses.challenge_paths_array'),
            'resource_modules.*.numeric'                       => __('responses.resource_modules_numeric'),
            'resource_modules.*.array'                         => __('responses.resource_modules_array'),
            'resource_groups.*.numeric'                        => __('responses.resource_groups_numeric'),
            'resource_groups.*.array'                          => __('responses.resource_groups_array'),
            'resource_collections.*.numeric'                   => __('responses.resource_collections_numeric'),
            'resource_collections.*.array'                     => __('responses.resource_collections_array'),
            'subject_line.max'                                 => __('responses.subject_line_max'),
            'email_body.max'                                   => __('responses.email_body_max'),
            'auto_invite.in'                                   => __('responses.choose_yes_no'),
            'invite_email.required'                            => __('responses.invite_email_required'),
            'invite_email.csv'                                 => __('responses.choose_csv_file'),
            'duration_id.required'                             => __('responses.duration_id_required'),
            'duration_id.exists'                               => __('responses.duration_id_exists'),
            'level_id.required'                                => __('responses.level_id_required'),
            'level_id.exists'                                  => __('responses.level_id_exists'),
            'live_event.url.required'                          => __('responses.event_url_required'),
            'live_event.is_verified.required'                  => __('responses.event_must_be_verified'),
            'live_event.is_verified.in'                        => __('responses.event_must_be_verified'),
            'integrate_campus_connect.in'                      => __('responses.integrate_campus_connect_in'),
            'campus_connect_job_title.required'                => __('responses.campus_connect_job_title_required'),
            'campus_connect_job_title.max'                     => __('responses.campus_connect_job_title_max'),
            'campus_connect_job_title.min'                     => __('responses.campus_connect_job_title_min'),
            'campus_connect_job_type.required'                 => __('responses.campus_connect_job_type_required'),
            'campus_connect_no_of_position.required'           => __('responses.campus_connect_no_of_position_required'),
            'campus_connect_qualification.required'            => __('responses.campus_connect_qualification_required'),
            'campus_connect_province.required'                 => __('responses.campus_connect_province_required'),
            'campus_connect_city.required'                     => __('responses.campus_connect_city_required'),
            'campus_connect_description.required'              => __('responses.campus_connect_description_required'),
            'campus_connect_deadline.required'                 => __('responses.campus_connect_deadline_required'),
            'campus_connect_hours_per_week.required'           => __('responses.campus_connect_hours_per_week_required'),
            'campus_connect_application_instructions.required' => __('responses.campus_connect_application_instructions_required'),
            'campus_connect_preferred_response.required'       => __('responses.campus_connect_preferred_response_required'),
            'campus_connect_schools.required'                  => __('responses.campus_connect_schools_required'),
            'campus_connect_salary_amount.required'            => __('responses.campus_connect_salary_amount_required'),
            'campus_connect_salary_amount.numeric'             => __('responses.campus_connect_salary_amount_numeric'),
            'campus_connect_salary_payment_frequency.required' => __('responses.campus_connect_salary_payment_frequency_required'),
            'campus_connect_application_email.required'        => __('responses.campus_connect_application_email_required'),
            'campus_connect_story_title.required'              => __('responses.campus_connect_story_title_required'),
            'campus_connect_story_title.max'                   => __('responses.campus_connect_story_title_max'),
            'campus_connect_story_title.min'                   => __('responses.campus_connect_story_title_min'),
            'campus_connect_story_body.required'               => __('responses.campus_connect_story_body_required'),
            'campus_connect_story_image.mimes'                 => __('responses.campus_connect_story_image_mimes'),

        ];
    }
}
