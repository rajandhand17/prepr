<?php

namespace App\Http\Requests\Manage\Challenge;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateChallengeRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $base_rules = [
            'organization_id'                       => 'required|exists:organizations,uuid',
            'category_id'                           => 'required|exists:categories,id',
            'duration_id'                           => 'required|exists:durations,id',
            'level_id'                              => 'required|exists:levels,id',
            'title'                                 => 'required|unique:challenges,title',
            'description'                           => 'required',
            'request_type'                          => 'required|in:draft,publish,archive',
            'privacy'                               => 'required|in:yes,no',
            'cover_image'                           => 'nullable|mimes:jpeg,jpg,png,webp|max:1024',
            'source_link'                           => 'nullable|url',
            'agreement'                             => 'required',
            'is_notification_enabled'               => 'in:yes,no',
            'project_privacy'                       => 'in:yes,no',
            'is_open'                               => 'in:yes,no',
            'is_auto_created'                       => 'in:yes,no',
            'achievement_image'                     => 'required|mimes:jpeg,jpg,png,webp|max:1024',
            'achievement_participation'             => 'required',
            'achievement_name'                      => 'required',
            'achievement_prize'                     => 'required|numeric',
            'achievement_points'                    => 'required|numeric',
            'skills'                                => 'required|array',
            'skills.*'                              => 'numeric|exists:skills,id',
            'skill_groups'                          => 'nullable|array',
            'skill_groups.*'                        => 'numeric|exists:skill_groups,id',
            'skill_stacks'                          => 'nullable|array',
            'skill_stacks.*'                        => 'numeric|exists:skill_stacks,id',
            'tags'                                  => 'required|array',
            'tags.*'                                => 'numeric|exists:tags,id',
            'tag_groups'                            => 'nullable|array',
            'tag_groups.*'                          => 'numeric|exists:tag_groups,id',
            'min_rank'                              => 'required|numeric',
            'min_points'                            => 'required|numeric',
            'project_submission_requirement_ids'    => 'required|array',
            'max_project_submission'                => 'required|numeric',
            'min_experience'                        => 'required|numeric',
            'min_imported_badges'                   => 'required|numeric',
            'min_achievement_counts'                => 'required|numeric',
            'template_id'                           => 'required|numeric',
        ];

        if ($this->request->has('winner_achievement_participation')) {
            $base_rules['winner_achievement_image'] = 'array';
            $base_rules['winner_achievement_image.*'] = 'required|mimes:jpeg,jpg,png,webp|max:1024';
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
            $base_rules['custom_timelines_date.*'] = 'required';
        }

        if ($this->has('assessment_title') !== null && $this->has('assessment_score') !== null && $this->has('assessment_weight') !== null) {
            $base_rules['assessment_title'] = 'array';
            $base_rules['assessment_title.*'] = 'required';
            $base_rules['assessment_score'] = 'array';
            $base_rules['assessment_score.*'] = 'required|numeric';
            $base_rules['assessment_weight'] = 'array';
            $base_rules['assessment_weight.*'] = 'required|numeric';
        }

        if ($this->request->has('assessment_type') !== null) {
            $base_rules['assessment_type'] = 'required|in:open,close';
            $base_rules['visibility'] = 'required|in:users,hidden';
            $base_rules['guidelines'] = 'required';
            $base_rules['attachments'] = 'required|mimes:jpeg,jpg,png,webp|max:1024';
            if ($this->assessment_type == 'close' && $this->members_email !== null) {
                $base_rules['members_email'] = 'array';
                $base_rules['members_email.*'] = 'required|email';
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

    public function message()
    {
        return [
            'organization_id.required'                    => __('responses.organization_id_required'),
            'organization_id.exists'                      => __('responses.organization_not_found'),
            'category_id.required'                        => __('responses.category_id_required'),
            'category_id.exists'                          => __('responses.category_not_found'),
            'duration_id.required'                        => __('responses.duration_id_required'),
            'duration_id.exists'                          => __('responses.duration_id_exists'),
            'level_id.required'                           => __('responses.level_id_required'),
            'level_id.exists'                             => __('responses.level_id_exists'),
            'title.required'                              => __('responses.title_required'),
            'title.unique'                                => __('responses.challenge_title_unique'),
            'description.required'                        => __('responses.description_required'),
            'request_type.required'                       => __('responses.request_type_required'),
            'privacy.in'                                  => __('responses.choose_yes_no'),
            'privacy.required'                            => __('responses.privacy_required'),
            'source_link'                                 => __('responses.challenge_source_link'),
            'is_notification_enabled.in'                  => __('responses.choose_yes_no'),
            'project_privacy.in'                          => __('responses.choose_yes_no'),
            'is_open'                                     => __('responses.choose_yes_no'),
            'is_auto_created'                             => __('responses.choose_yes_no'),
            'achievement_name.required'                   => __('responses.achievement_name_required'),
            'achievement_points.required'                 => __('responses.achievement_points_required'),
            'achievement_prize.required'                  => __('responses.achievement_prize_required'),
            'achievement_image.required'                  => __('responses.achievement_image_required'),
            'skills.required'                             => __('responses.skills_required'),
            'skills.required_if'                          => __('responses.skill_not_found'),
            'skill_groups.*.exists'                       => __('responses.skill_groups_not_exists'),
            'skill_groups.*.array'                        => __('responses.skill_groups_array'),
            'skill_stacks.*.array'                        => __('responses.skill_stacks_array'),
            'skill_stacks.*.exists'                       => __('responses.skill_stack_not_found'),
            'tags.required'                               => __('responses.tags_required'),
            'tags.numeric'                                => __('responses.tags_numeric'),
            'tag_groups.*.exists'                         => __('responses.tag_groups_not_found'),
            'tag_groups.*.array'                          => __('responses.tag_groups_array'),
            'tag_groups.*.numeric'                        => __('responses.tag_groups_numeric'),
            'winner_achievement_image.required'           => __('responses.winner_achievement_image_required'),
            'winner_achievement_image.array'              => __('responses.winner_achievement_image_array'),
            'winner_achievement_participation.required'   => __('responses.winner_achievement_participation_required'),
            'winner_achievement_participation.array'      => __('responses.winner_achievement_participation_array'),
            'winner_achievement_name.required'            => __('responses.winner_achievement_name_required'),
            'winner_achievement_name.array'               => __('responses.winner_achievement_name_array'),
            'winner_achievement_prize.required'           => __('responses.winner_achievement_prize_required'),
            'winner_achievement_prize.array'              => __('responses.winner_achievement_prize_array'),
            'winner_achievement_prize.required'           => __('responses.winner_achievement_points_required'),
            'winner_achievement_prize.array'              => __('responses.winner_achievement_points_array'),
            'achievement_conditions.required'             => __('responses.achievement_conditions_required'),
            'achievement_conditions.array'                => __('responses.achievement_conditions_array'),
            'host_id.required'                            => __('responses.host_id_required'),
            'host_id.array'                               => __('responses.host_id_array'),
            'project_submission_requirement_ids.required' => __('responses.project_submission_requirement_ids_required'),
            'project_submission_requirement_ids.array'    => __('responses.project_submission_requirement_ids_array'),
            'custom_timelines_title.required'             => __('responses.custom_timelines_title_required'),
            'custom_timelines_title.array'                => __('responses.custom_timelines_title_array'),
            'custom_timelines_date.required'              => __('responses.custom_timelines_date_required'),
            'custom_timelines_date.array'                 => __('responses.custom_timelines_date_array'),
        ];
    }
}
