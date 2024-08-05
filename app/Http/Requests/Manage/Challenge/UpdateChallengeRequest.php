<?php

namespace App\Http\Requests\Manage\Challenge;

use App\Services\Manage\ChallengeService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateChallengeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        $challenge = ChallengeService::getChallengeBasedOnSlug(request()->route('slug'));
        if (!$challenge) {
            throw new NotFoundHttpException();
        }

        $base_rules = [
            'request_type'                          => 'required|in:draft,publish',
            'is_auto_created'                       => 'required_if:request_type,publish|in:yes,no',
            'is_ai_created'                         => 'required_if:request_type,publish|in:yes,no',
            'title'                                 => 'required_if:request_type,publish|max:255|unique:challenges,title,'.$challenge->id,
            'description_type'                      => 'required_if:request_type,publish|in:text,scorm',
            'description'                           => 'required_if:description_type,text',
            'duration_id'                           => 'nullable|exists:durations,id',
            'level_id'                              => 'nullable|exists:levels,id',
            'skills'                                => 'required_if:request_type,publish|array',
            'skills.*'                              => 'numeric|exists:skills,id',
            'is_open'                               => 'required|in:yes,no',
            'privacy'                               => 'required|in:yes,no',
            'project_privacy'                       => 'required|in:yes,no',
            'is_notification_enabled'               => 'required|in:yes,no',
            'cover_banner_type'                     => 'required_if:request_type,publish|in:image,embedded,none',
            'skill_groups'                          => 'nullable|array',
            'skill_groups.*'                        => 'numeric|exists:skill_groups,id',
            'skill_stacks'                          => 'nullable|array',
            'skill_stacks.*'                        => 'numeric|exists:skill_stacks,id',
            'host_id'                               => 'nullable|array',
            'host_id.*'                             => 'numeric|exists:hosts,id',
            'type'                                  => 'nullable|array',
            'type.*'                                => 'in:assess,onboard,engage,grow',
            'mode'                                  => 'nullable|array',
            'mode.*'                                => 'in:team,individual',
            'source_link'                           => 'nullable|url',
            'category_id'                           => 'nullable|exists:categories,id',
            'jobs'                                  => 'nullable|array',
            'jobs.*'                                => 'numeric|exists:job_titles,id',
            'external_links'                        => 'array',
            'external_link_ids'                     => 'array|exists:social_links,id',
            'external_links.*'                      => 'url|max:700',
            'external_link_ids.*'                   => 'numeric',
            'template_type'                         => 'required_if:request_type,publish|in:existing,new',
            'template_id'                           => 'required_if:template_type,existing|numeric|exists:pitch_templates,id',
            'project_submission_requirement_ids'    => 'required_if:request_type,publish|array',
            'allow_submit_project'                  => 'in:yes,no',
            'complete_education_program'            => 'in:yes,no',
            'complete_experience'                   => 'in:yes,no',
            'min_experience'                        => 'required_if:complete_experience,yes|numeric',
            'agreement'                             => 'required_if:request_type,publish',
            'achievement_image'                     => 'mimes:jpeg,jpg,png,webp|max:1024',
            'achievement_name'                      => 'nullable',
            'achievement_prize'                     => 'nullable|numeric',
            'achievement_points'                    => 'nullable|numeric',
            'winner_achievement_participation'      => 'required_if:request_type,publish|array',
            'winner_achievement_participation.*'    => 'in:yes,no',
            'winner_achievement_image'              => 'nullable|array',
            'winner_achievement_image.*'            => 'mimes:jpeg,jpg,png,webp|max:5120',
            'winner_achievement_name'               => 'nullable|array',
            'winner_achievement_name.*'             => 'string',
            'winner_achievement_prize'              => 'nullable|array',
            'winner_achievement_prize.*'            => 'numeric',
            'winner_achievement_point'              => 'nullable|array',
            'winner_achievement_point.*'            => 'numeric',
            'max_project_submission'                => 'nullable|numeric',
            'max_project_associated'                => 'nullable|numeric',
            'min_imported_badges'                   => 'nullable|numeric',
            'min_achievement_counts'                => 'nullable|numeric',
            'min_rank'                              => 'nullable|numeric',
            'min_points'                            => 'nullable|numeric',
            'additional_requirements'               => 'nullable|string',
            'assessment_type'                       => 'required_if:request_type,publish|in:open,closed,ai,none',
            'guidelines'                            => 'required_if:assessment_type,open,closed,ai',
            'visibility'                            => 'required_if:assessment_type,closed,ai|in:users,hidden',
            'attachments'                           => 'max:5120',
            'timeline_type'                         => 'required_if:request_type,publish|in:restricted,flexible',
            'integrate_campus_connect'              => 'required_if:request_type,publish|in:both,job,story,no',
        ];

        // Add scorm_file rule based on the condition
        $description_type = $this->get('description_type');
        if ($description_type == 'scorm') {
            if (!$challenge->scorm()->exists()) {
                $base_rules['scorm_file'] = 'required_if:description_type,scorm|file|mimes:zip|max:500000';
            } else {
                $base_rules['scorm_file'] = 'nullable|file|mimes:zip|max:500000';
            }
        }

        // Challenge cover image validation
        if ($this->has('cover_banner_type') && $this->input('cover_banner_type') == 'image') {
            if ($challenge->media != null && $challenge->getRawOriginal('media') == 'default_images/challenge.webp') {
                $base_rules['cover_image'] = [
                    'required',
                    'mimes:jpeg,jpg,png,webp',
                    'max:5120',
                    function ($attribute, $value, $fail) {
                        if ($value && $value->isValid()) {
                            $image = getimagesize($value);
                            if ($image[0] < 625 || $image[1] < 355) {
                                $fail(''.$attribute.' must be at least 625x355 pixels.');
                            }
                        }
                    },
                ];
            }
        }

        if ($this->has('cover_banner_type') && $this->input('cover_banner_type') == 'embedded') {
            $regexYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexNoCookieYoutube = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/www.youtube-nocookie.com\/(?:\b|_).*?(?:\b|_)iframe>/';
            $regexVimeo = '/<iframe(?:\b|_).*?(?:\b|_)src="https:\/\/player.vimeo.com\/(?:\b|_).*?(?:\b|_)iframe>/';

            $cover_embedded = $this->input('cover_embedded');
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

            $base_rules['cover_embedded'] = [
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

        // Challenge Association code with labs
        if ($this->request->has('labs')) {
            $base_rules['labs'] = 'array';
            $base_rules['labs.*'] = 'exists:labs,uuid';
        }

        // Challenge Association code with lab programs
        if ($this->request->has('lab_programs')) {
            $base_rules['lab_programs'] = 'array';
            $base_rules['lab_programs.*'] = 'exists:lab_programs,uuid';
        }

        // Challenge Association code with resource module
        if ($this->request->has('resource_modules')) {
            $base_rules['resource_modules'] = 'array';
            $base_rules['resource_modules.*'] = 'exists:resource_modules,uuid';
        }

        // Challenge Association code with resource collection
        if ($this->request->has('resource_collections')) {
            $base_rules['resource_collections'] = 'array';
            $base_rules['resource_collections.*'] = 'exists:resource_collections,uuid';
        }

        // Challenge Association code with resource group
        if ($this->request->has('resource_groups')) {
            $base_rules['resource_groups'] = 'array';
            $base_rules['resource_groups.*'] = 'exists:resource_groups,uuid';
        }

        if ($this->request->get('assessment_type') == 'closed') {
            $base_rules['members_email'] = 'array|required';
            $base_rules['members_email.*'] = 'email';
        }

        // Challenge Template new adding code
        if ($this->request->has('template_type') && $this->input('template_type') == 'new') {
            $base_rules['pitch_questions'] = 'array';
            $base_rules['pitch_questions.*'] = 'nullable';
            $base_rules['pitch_questions_description'] = 'array';
            $base_rules['pitch_questions_description.*'] = 'nullable';
            $base_rules['task_questions'] = 'array';
            $base_rules['task_questions.*'] = 'nullable';
        }

        // Challenge assessment only if its not set to none
        if ($this->has('assessment_type') && $this->input('assessment_type') != 'none') {
            $base_rules['assessment_title'] = 'required|array';
            $base_rules['assessment_title.*'] = 'required_if:assessment_type,open,closed';
            $base_rules['assessment_score'] = 'required|array';
            $base_rules['assessment_score.*'] = 'required_if:assessment_type,open,closed|numeric';
            $base_rules['assessment_weight'] = [
                'required',
                'array',
                'required_if:assessment_type,open,closed',
                function ($attribute, $value, $fail) {
                    if (array_sum($value) != 100) {
                        $fail(__('responses.challenge_weight_should_be_100'));
                    }
                },
            ];
            $base_rules['assessment_weight.*'] = 'required_if:assessment_type,open,closed|numeric';
        }

        // For challenge restricted timeline
        if ($this->has('timeline_type') && $this->input('timeline_type') == 'restricted') {
            $base_rules['start_date'] = ['required_if:request_type,publish', 'after_or_equal:'.Carbon::now()->toDateTimeString()];
            $base_rules['start_date_description'] = 'required_if:request_type,publish';
            $base_rules['registration_deadline_date'] = ['nullable', 'after_or_equal:start_date'];
            $base_rules['registration_deadline_date_description'] = 'nullable';
            $base_rules['submission_deadline_date'] = ['required_if:request_type,publish', 'after_or_equal:registration_deadline_date'];
            $base_rules['submission_deadline_date_description'] = 'required_if:request_type,publish';
        }

        // For challenge flexible timeline
        if ($this->has('timeline_type') && $this->input('timeline_type') == 'flexible') {
            $base_rules['flexible_date_number'] = 'required_if:request_type,publish|numeric';
            $base_rules['flexible_date_duration'] = 'required_if:request_type,publish|in:days,weeks,months';
            $base_rules['flexible_expire_deadline'] = ['nullable', 'after_or_equal:'.Carbon::now()->toDateTimeString()];
            $base_rules['automatic_alert'] = 'required_if:request_type,publish|in:day,week';
        }

        // For challenge custom notify only if flexible timeline
        if ($this->has('timeline_type') && $this->input('timeline_type') == 'flexible') {
            $base_rules['schedule_custom_notify'] = 'nullable|array';
            $base_rules['schedule_custom_notify.*'] = 'in:yes,no';
            $base_rules['custom_timelines_title'] = 'nullable|array';
            $base_rules['custom_timelines_title.*'] = 'string';
            $base_rules['custom_timelines_number'] = 'nullable|array';
            $base_rules['custom_timelines_number.*'] = 'integer|max:100';
            $base_rules['custom_timelines_duration'] = 'nullable|array';
            $base_rules['custom_timelines_duration.*'] = 'in:days,weeks,months';
            $base_rules['custom_timelines_description'] = 'nullable|array';
            $base_rules['custom_timelines_description.*'] = 'string';
        }

        // For challenge custom announcement only if flexible timeline and schedule custom notify is yes
        if ($this->has('timeline_type') && $this->input('timeline_type') == 'flexible') {
            $base_rules['custom_flexible_announcement'] = 'nullable|array';
            $base_rules['custom_flexible_announcement.*'] = 'required_if:schedule_custom_notify.*,yes|in:yes,no';
            $base_rules['custom_announcement_type'] = 'nullable|array';
            $base_rules['custom_announcement_type.*'] = 'required_if:custom_flexible_announcement.*,yes|in:email,notification';
            $base_rules['custom_announcement_number'] = 'nullable|array';
            $base_rules['custom_announcement_number.*'] = 'integer|max:100';
            $base_rules['custom_announcement_duration'] = 'nullable|array';
            $base_rules['custom_announcement_duration.*'] = 'required_if:custom_flexible_announcement.*,yes|in:days,weeks,months';
            $base_rules['custom_announcement_description'] = 'nullable|array';
            $base_rules['custom_announcement_description.*'] = 'string';
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
        return $base_rules;
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $assessment_title = $this->input('assessment_title', []);
            $assessment_score = $this->input('assessment_score', []);

            $count_title = count($assessment_title);
            $count_score = count($assessment_score);

            if (($count_title > 0 || $count_score > 0) &&
                ($count_title !== $count_score)
            ) {
                $validator->errors()->add('assessment_data', __('responses.title_score_should_match_count'));
            }

            // Custom validation rule to check at least one of pitch_questions or task_questions is not empty
            if ($this->request->has('template_type') && $this->input('template_type') == 'new') {
                if (empty($this->input('pitch_questions')) && empty($this->input('task_questions'))) {
                    $validator->errors()->add('at_least_one_question', 'Either pitch questions or task questions must be provided.');
                }
            }
        });
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
