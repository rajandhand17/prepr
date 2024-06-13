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
            'cover_image'                        => 'image|mimes:jpeg,jpg,png,webp|max:1024',
            'title'                              => 'required_if:request_type,publish|max:255|unique:challenges,title,'.$challenge->id,
            'request_type'                       => 'required|in:draft,publish,archive',
            'description'                        => 'required_if:request_type,publish|nullable',
            'category_id'                        => 'required|exists:categories,id',
            'duration_id'                        => 'required|exists:durations,id',
            'level_id'                           => 'required|exists:levels,id',
            'privacy'                            => 'required_if:request_type,publish|in:yes,no',
            'skills'                             => 'required_if:request_type,publish|nullable|array',
            'skills.*'                           => 'required_if:request_type,publish|numeric',
            'skill_groups'                       => 'nullable|array',
            'skill_groups.*'                     => 'numeric',
            'skill_stacks'                       => 'nullable|array',
            'skill_stacks.*'                     => 'required_if:request_type,publish|numeric',
            'tags'                               => 'required_if:request_type,publish|nullable|array',
            'tags.*'                             => 'numeric',
            'tag_groups'                         => 'nullable|array',
            'tag_groups.*'                       => 'numeric',
            'is_notification_enabled'            => 'in:yes,no',
            'source_link'                        => 'nullable|url',
            'agreement'                          => 'required',
            'project_privacy'                    => 'in:yes,no',
            'is_open'                            => 'in:yes,no',
            'is_auto_created'                    => 'in:yes,no',
            'achievement_image'                  => 'mimes:jpeg,jpg,png,webp|max:1024',
            'achievement_participation'          => 'required',
            'achievement_name'                   => 'required',
            'achievement_prize'                  => 'required|numeric',
            'achievement_points'                 => 'required|numeric',
            'min_rank'                           => 'required|numeric',
            'min_points'                         => 'required|numeric',
            'project_submission_requirement_ids' => 'required|array',
            'max_project_submission'             => 'required|numeric',
            'min_experience'                     => 'required|numeric',
            'min_imported_badges'                => 'required|numeric',
            'min_achievement_counts'             => 'required|numeric',
            'template_id'                        => 'required|numeric',
            'allow_submit_project'               => 'in:yes,no',
            'requirement_program'                => 'in:yes,no',
            'complete_education_program'         => 'in:yes,no',
            'complete_experience'                => 'in:yes,no',
            'timeline_type'                      => 'required|in:restricted,flexible',
            'integrate_campus_connect'           => 'in:both,job,story,no',
        ];

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
        if ($this->request->has('winner_achievement_participation')) {
            $base_rules['winner_achievement_image'] = 'array';
            $base_rules['winner_achievement_image.*'] = 'mimes:jpeg,jpg,png,webp|max:1024';
            $base_rules['winner_achievement_participation'] = 'array';
            $base_rules['winner_achievement_participation.*'] = 'in:incentive';
            $base_rules['winner_achievement_name'] = 'array';
            $base_rules['winner_achievement_name.*'] = 'required';
            $base_rules['winner_achievement_prize'] = 'array';
            $base_rules['winner_achievement_prize.*'] = 'required|numeric';
            $base_rules['winner_achievement_point'] = 'array';
            $base_rules['winner_achievement_point.*'] = 'required|numeric';
        }

        if ($this->request->has('host_id') !== null) {
            $base_rules['host_id'] = 'array';
            $base_rules['host_id.*'] = 'required|numeric';
        }

        if ($this->has('timeline_type') == 'flexible' && $this->request->has('custom_timelines_title') !== null && $this->request->has('custom_timelines_date') !== null) {
            $base_rules['custom_timelines_title'] = 'array';
            $base_rules['custom_timelines_title.*'] = 'required';
            $base_rules['custom_timelines_date'] = 'array';
            $base_rules['custom_timelines_date.*'] = ['required', 'after_or_equal:'.Carbon::now()->toDateTimeString()];
            $base_rules['schedule_custom_notify'] = 'array|required';
            $base_rules['schedule_custom_notify.*'] = 'in:0,1';
        }

        if ($this->has('assessment_title') !== null && $this->has('assessment_score') !== null && $this->has('assessment_weight') !== null) {
            $base_rules['assessment_title'] = 'array';
            $base_rules['assessment_title.*'] = 'required';
            $base_rules['assessment_description'] = 'array';
            $base_rules['assessment_score'] = 'array';
            $base_rules['assessment_score.*'] = 'required|numeric';
            $base_rules['assessment_weight'] = 'array';
            $base_rules['assessment_weight.*'] = 'required|numeric';
        }

        if ($this->request->has('assessment_type')) {
            $base_rules['assessment_type'] = 'in:open,closed,ai';
            $base_rules['guidelines'] = 'required_if:assessment_type,open,closed,ai';
            $base_rules['attachments'] = 'max:5120';

            if ($this->request->get('assessment_type') == 'closed') {
                $base_rules['visibility'] = 'in:users,hidden';
                $base_rules['members_email'] = 'array|required';
                $base_rules['members_email.*'] = 'email';
            }
        }

        if ($this->has('timeline_type') && $this->input('timeline_type') === 'restricted') {
            $base_rules['open_call_date'] = ['required_if:request_type,publish', 'after_or_equal:'.Carbon::now()->toDateTimeString()];
            $base_rules['open_call_date_description'] = 'required_if:request_type,publish';
            $base_rules['last_call_date'] = ['required_if:request_type,publish', 'after_or_equal:'.Carbon::now()->toDateTimeString()];
            $base_rules['last_call_date_description'] = 'required_if:request_type,publish';
            $base_rules['application_deadline_date'] = ['required_if:request_type,publish', 'after_or_equal:open_call_date'];
            $base_rules['application_deadline_date_description'] = 'required_if:request_type,publish';
            $base_rules['submission_deadline_date'] = ['required_if:request_type,publish', 'after_or_equal:application_deadline_date'];
            $base_rules['submission_deadline_date_description'] = 'required_if:request_type,publish';
        }

        if ($this->has('timeline_type') && $this->input('timeline_type') === 'flexible') {
            $base_rules['automatic_alert'] = 'required|in:0,1';
            $base_rules['flexible_date_number'] = 'required_if:request_type,publish';
            $base_rules['flexible_date_duration'] = 'required_if:request_type,publish';
            $base_rules['flexible_expire_deadline'] = ['required_if:request_type,publish', 'after_or_equal:'.Carbon::now()->toDateTimeString()];
        }

        if ($this->request->has('labs')) {
            $base_rules['labs'] = 'array';
            $base_rules['labs.*'] = 'exists:labs,uuid';
        }

        if ($this->request->has('lab_programs')) {
            $base_rules['lab_programs'] = 'array';
            $base_rules['lab_programs.*'] = 'exists:lab_programs,uuid';
        }

        if ($this->request->has('resource_modules')) {
            $base_rules['resource_modules'] = 'array';
            $base_rules['resource_modules.*'] = 'exists:resource_modules,uuid';
        }

        if ($this->request->has('resource_collections')) {
            $base_rules['resource_collections'] = 'array';
            $base_rules['resource_collections.*'] = 'exists:resource_collections,uuid';
        }

        if ($this->request->has('resource_groups')) {
            $base_rules['resource_groups'] = 'array';
            $base_rules['resource_groups.*'] = 'exists:resource_groups,uuid';
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
            'category_id.required'                             => __('responses.category_id_required'),
            'category_id.exists'                               => __('responses.category_not_found'),
            'duration_id.required'                             => __('responses.duration_id_required'),
            'duration_id.exists'                               => __('responses.duration_id_exists'),
            'level_id.required'                                => __('responses.level_id_required'),
            'level_id.exists'                                  => __('responses.level_id_exists'),
            'title.required_if'                                => __('responses.title_required'),
            'title.unique'                                     => __('responses.challenge_title_unique'),
            'description.required_if'                          => __('responses.description_required'),
            'request_type.required'                            => __('responses.request_type_required'),
            'privacy.in'                                       => __('responses.choose_yes_no'),
            'privacy.required_if'                              => __('responses.privacy_required'),
            'source_link'                                      => __('responses.challenge_source_link'),
            'is_notification_enabled.in'                       => __('responses.choose_yes_no'),
            'project_privacy.in'                               => __('responses.choose_yes_no'),
            'is_open'                                          => __('responses.choose_yes_no'),
            'is_auto_created'                                  => __('responses.choose_yes_no'),
            'achievement_name.required'                        => __('responses.achievement_name_required'),
            'achievement_points.required'                      => __('responses.achievement_points_required'),
            'achievement_prize.required'                       => __('responses.achievement_prize_required'),
            'achievement_image.required'                       => __('responses.achievement_image_required'),
            'skills.required'                                  => __('responses.skills_required'),
            'skills.required_if'                               => __('responses.skill_not_found'),
            'skill_groups.*.exists'                            => __('responses.skill_groups_not_exists'),
            'skill_groups.*.array'                             => __('responses.skill_groups_array'),
            'skill_stacks.*.array'                             => __('responses.skill_stacks_array'),
            'skill_stacks.*.exists'                            => __('responses.skill_stack_not_found'),
            'tags.required'                                    => __('responses.tags_required'),
            'tags.numeric'                                     => __('responses.tags_numeric'),
            'tag_groups.*.exists'                              => __('responses.tag_groups_not_found'),
            'tag_groups.*.array'                               => __('responses.tag_groups_array'),
            'tag_groups.*.numeric'                             => __('responses.tag_groups_numeric'),
            'winner_achievement_image.required'                => __('responses.winner_achievement_image_required'),
            'winner_achievement_image.array'                   => __('responses.winner_achievement_image_array'),
            'winner_achievement_participation.required'        => __('responses.winner_achievement_participation_required'),
            'winner_achievement_participation.array'           => __('responses.winner_achievement_participation_array'),
            'winner_achievement_name.required'                 => __('responses.winner_achievement_name_required'),
            'winner_achievement_name.array'                    => __('responses.winner_achievement_name_array'),
            'winner_achievement_prize.required'                => __('responses.winner_achievement_prize_required'),
            'winner_achievement_prize.array'                   => __('responses.winner_achievement_prize_array'),
            'winner_achievement_points.required'               => __('responses.winner_achievement_points_required'),
            'winner_achievement_points.array'                  => __('responses.winner_achievement_points_array'),
            'achievement_conditions.required'                  => __('responses.achievement_conditions_required'),
            'achievement_conditions.array'                     => __('responses.achievement_conditions_array'),
            'host_id.required'                                 => __('responses.host_id_required'),
            'host_id.array'                                    => __('responses.host_id_array'),
            'project_submission_requirement_ids.required'      => __('responses.project_submission_requirement_ids_required'),
            'project_submission_requirement_ids.array'         => __('responses.project_submission_requirement_ids_array'),
            'custom_timelines_title.required'                  => __('responses.custom_timelines_title_required'),
            'custom_timelines_title.array'                     => __('responses.custom_timelines_title_array'),
            'custom_timelines_date.required'                   => __('responses.custom_timelines_date_required'),
            'custom_timelines_date.array'                      => __('responses.custom_timelines_date_array'),
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
