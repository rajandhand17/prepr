<?php

namespace App\Http\Resources\Manage\Challenge;

use App\Services\Manage\ChallengeSponsorService;
use App\Services\ProjectSubmissionRequirementService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\TagGroupService;
use App\Services\TagService;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayal|\JsonSerializable
     */
    public function toArray($request)
    {
        $achievement = [];
        $incentive_achievement = [];
        $hosts = [];
        $incentive_achievement = [];

        if ($this->getCategory) {
            $category = $this->getCategory->title;
            $category_id = $this->getCategory->id;
        }

        if ($this->durations) {
            $duration = $this->durations->title;
            $duration_id = $this->durations->id;
        }

        if ($this->levels) {
            $level = $this->levels->title;
            $level_id = $this->levels->id;
        }

        if ($this->skills) {
            $associatedSkills = $this->skills->pluck('foreign_id');
            $skills = SkillService::getSkillBasedOnIds($associatedSkills)->pluck('title', 'id');
        }

        if ($this->skill_groups) {
            $associatedSkillGroups = $this->skill_groups->pluck('foreign_id');
            $skill_groups = SkillGroupService::getSkillGroupsBasedOnIds($associatedSkillGroups)->pluck('title', 'id');

            if ($skill_groups->isEmpty()) {
                $skill_groups = $this->skill_groups->pluck('foreign_id');
            }
        }

        if ($this->skill_stacks) {
            $associatedSkillStacks = $this->skill_stacks->pluck('foreign_id');
            $skill_stacks = SkillStackService::getSkillStacksBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        if ($this->tags) {
            $associatedSkillStacks = $this->tags->pluck('foreign_id');
            $tags = TagService::getTagsBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        if ($this->tag_groups) {
            $associatedSkillStacks = $this->tag_groups->pluck('foreign_id');
            $tag_groups = TagGroupService::getTagGroupsBasedOnIds($associatedSkillStacks)->pluck('title', 'id');
        }

        if ($this->participation_achievement) {
            $achievement = [
                'achievement_name'      => $this->participation_achievement->achievement_name,
                'achievement_points'    => $this->participation_achievement->achievement_points,
                'achievement_image'     => $this->participation_achievement->achievement_image,
                'achievement_prize'     => $this->participation_achievement->achievement_prize,
            ];
        }

        if ($this->incentive_achievement) {
            $incentive_achievement = $this->incentive_achievement->map(function ($item) {
                return [
                    'achievement_name'   => $item->achievement_name,
                    'achievement_points' => $item->achievement_points,
                    'achievement_image'  => $item->achievement_image,
                    'achievement_prize'  => $item->achievement_prize,
                ];
            });
        }

        if ($this->challenge_requirements) {
            $challenge_conditions = [];
            foreach ($this->challenge_requirements->project_submission_requirement_ids as $project_submission_requirement) {
                $check_achievement_condition = ProjectSubmissionRequirementService::getProjectSubmissionRequirementByID($this->language, $project_submission_requirement);
                $challenge_conditions[$check_achievement_condition->id] = $check_achievement_condition->title;
            }
            $challenge_requirements = [
                'minimum_rank'                     => $this->challenge_requirements->min_rank,
                'minimum_points'                   => $this->challenge_requirements->min_points,
                'maximum_submission'               => $this->challenge_requirements->max_project_submission,
                'minimum_experience'               => $this->challenge_requirements->min_experience,
                'minimum_import_badges'            => $this->challenge_requirements->min_imported_badges,
                'minimum_achievement_count'        => $this->challenge_requirements->min_achievement_counts,
                'challenge_completion_requirement' => json_decode(json_encode($challenge_conditions)),
            ];
        }

        if ($this->hosts) {
            $associatedHosts = $this->hosts->pluck('host_id');
            $hosts = ChallengeSponsorService::getHostBasedOnIds($associatedHosts)->pluck('title', 'id');
        }

        if ($this->challenge_assessment_criteria) {
            $challenge_assessment_criteria = $this->challenge_assessment_criteria->map(function ($item) {
                return [
                    'assessment_title'   => $item->title,
                    'assessment_score'   => $item->score,
                    'assessment_weight'  => $item->weight,
                ];
            });
        }

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'user'                          => UserService::joinName($this->user->first_name, $this->user->last_name),
            'organization_id'               => $this->organization->uuid,
            'organization'                  => $this->organization->title,
            'category_id'                   => $category_id,
            'category'                      => $category,
            'duration'                      => $duration,
            'duration_id'                   => $duration_id,
            'level'                         => $level,
            'level_id'                      => $level_id,
            'slug'                          => $this->slug,
            'title'                         => $this->title,
            'description'                   => $this->description,
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'media_type'                    => $this->media_type,
            'media'                         => $this->media,
            'status'                        => ($this->status == '0') ? 'draft' : (($this->status == '1') ? 'published' : 'archive'),
            'source_link'                   => $this->source_link,
            'agreement'                     => $this->agreement,
            'is_notification_enabled'       => ($this->is_notification_enabled == '1') ? 'yes' : 'no',
            'project_privacy'               => ($this->project_privacy == '1') ? 'yes' : 'no',
            'is_open'                       => ($this->is_open == '1') ? 'yes' : 'no',
            'is_auto_created'               => ($this->is_auto_created == '1') ? 'yes' : 'no',
            'skills'                        => $skills,
            'skill_groups'                  => $skill_groups,
            'skill_stacks'                  => $skill_stacks,
            'tags'                          => $tags,
            'tag_groups'                    => $tag_groups,
            'participation_achievement'     => $achievement,
            'incentive_achievement'         => $incentive_achievement,
            'challenge_requirements'        => $challenge_requirements,
            'Sponsors'                      => $hosts,
            'challenge_assessment_criteria' => $challenge_assessment_criteria,
        ];
    }
}
