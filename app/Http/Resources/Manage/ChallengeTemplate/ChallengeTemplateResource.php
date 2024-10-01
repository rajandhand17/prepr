<?php

namespace App\Http\Resources\Manage\ChallengeTemplate;

use App\Http\Resources\Manage\Lab\LabListNameResource;
use App\Http\Resources\Manage\LabProgram\LabProgramListNameResource;
use App\Http\Resources\Manage\Organization\OrganizationHostResource;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionListNameResource;
use App\Http\Resources\Manage\ResourceGroup\ResourceGroupListNameResource;
use App\Http\Resources\Manage\ResourceModule\ResourceModuleListNameResource;
use App\Http\Resources\Manage\Scorm\ScormResource;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\ChallengeSponsorService;
use App\Services\Manage\ChallengeTemplateService;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceGroupService;
use App\Services\Manage\ResourceModuleService;
use App\Services\ProjectSubmissionRequirementService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\UserService;
use Illuminate\Http\Resources\Json\JsonResource;

class ChallengeTemplateResource extends JsonResource
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
        $category_id = null;
        $category = null;
        $duration = null;
        $duration_id = null;
        $level = null;
        $level_id = null;
        $skills = null;
        $skill_groups = null;
        $skill_stacks = null;
        $achievement = null;
        $incentive_achievement = null;
        $challenge_requirements = null;
        $hosts = null;
        $challenge_assessment_criteria = null;
        $challenge_assessment = null;
        $challenge_timelines = null;
        $challenge_custom_timelines = null;
        $labs = [];
        $lab_programs = [];
        $resource_modules = [];
        $resource_collections = [];
        $resource_groups = [];

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
                if ($check_achievement_condition) {
                    $challenge_conditions[$check_achievement_condition->id] = $check_achievement_condition->title;
                }
            }
            switch ($this->challenge_requirements->allow_submit_project) {
                case '0':
                    $allow_submit_project = 'no';
                    break;
                case '1':
                    $allow_submit_project = 'yes';
                    break;
                default:
                    $allow_submit_project = 'no';
                    break;
            }

            switch ($this->challenge_requirements->requirement_program) {
                case '0':
                    $requirement_program = 'no';
                    break;
                case '1':
                    $requirement_program = 'yes';
                    break;
                default:
                    $requirement_program = 'no';
                    break;
            }

            switch ($this->challenge_requirements->complete_education_program) {
                case '0':
                    $complete_education_program = 'no';
                    break;
                case '1':
                    $complete_education_program = 'yes';
                    break;
                default:
                    $complete_education_program = 'no';
                    break;
            }

            switch ($this->challenge_requirements->complete_experience) {
                case '0':
                    $complete_experience = 'no';
                    break;
                case '1':
                    $complete_experience = 'yes';
                    break;
                default:
                    $complete_experience = 'no';
                    break;
            }

            $challenge_requirements = [
                'min_rank'                              => $this->challenge_requirements->min_rank,
                'min_points'                            => $this->challenge_requirements->min_points,
                'max_project_submission'                => $this->challenge_requirements->max_project_submission,
                'max_project_associated'                => $this->challenge_requirements->max_project_associate,
                'min_experience'                        => $this->challenge_requirements->min_experience,
                'min_imported_badges'                   => $this->challenge_requirements->min_imported_badges,
                'min_achievement_counts'                => $this->challenge_requirements->min_achievement_counts,
                'additional_requirements'               => $this->challenge_requirements->additional_requirements,
                'allow_submit_project'                  => $allow_submit_project,
                'requirement_program'                   => $requirement_program,
                'complete_education_program'            => $complete_education_program,
                'complete_experience'                   => $complete_experience,
                'project_submission_requirement_ids'    => json_decode(json_encode($challenge_conditions)),
            ];
        }

        if ($this->hosts) {
            $associatedHosts = $this->hosts->pluck('host_id');
            $hosts = ChallengeSponsorService::getHostBasedOnIds($associatedHosts)->pluck('id');
        }

        if ($this->challenge_assessment_criteria) {
            $challenge_assessment_criteria = $this->challenge_assessment_criteria->map(function ($item) {
                return [
                    'assessment_title'        => $item->title,
                    'assessment_description'  => $item->description,
                    'assessment_score'        => $item->score,
                    'assessment_weight'       => $item->weight,
                ];
            });
        }

        if ($this->challenge_assessment) {
            $challenge_assessment = ChallengeAssessmentService::getChallengeAssessmentData($this->challenge_assessment);
        }

        if ($this->challenge_timelines) {
            if ($this->challenge_timelines->timeline_type == '0') {
                $challenge_timelines = [
                    'timeline_type'                 => 'flexible',
                    'flexible_date_number'          => $this->challenge_timelines->flexible_date_number,
                    'flexible_date_duration'        => $this->challenge_timelines->flexible_date_duration,
                    'automatic_alert'               => $this->challenge_timelines->automatic_alert == '0' ? 'day' : 'week',
                    'flexible_expire_deadline'      => $this->challenge_timelines->flexible_expire_deadline,
                ];
            } elseif ($this->challenge_timelines->timeline_type == '1') {
                $challenge_timelines = [
                    'timeline_type'                             => 'restricted',
                    'start_date'                                => $this->challenge_timelines->start_date,
                    'start_date_description'                    => $this->challenge_timelines->start_date_description,
                    'registration_deadline_date'                => $this->challenge_timelines->registration_deadline_date,
                    'registration_deadline_date_description'    => $this->challenge_timelines->registration_deadline_date_description,
                    'submission_deadline_date'                  => $this->challenge_timelines->submission_deadline_date,
                    'submission_deadline_date_description'      => $this->challenge_timelines->submission_deadline_date_description,
                    'challenge_duration'                        => $this->challenge_timelines->challenge_duration,
                ];
            }
        }

        if ($this->challenge_custom_timelines) {
            $challenge_custom_timelines = $this->challenge_custom_timelines->map(function ($item) {
                return [
                    'custom_timelines_title'       => $item->custom_timelines_title,
                    'custom_timelines_number'      => $item->custom_timelines_number,
                    'custom_timelines_description' => $item->custom_timelines_description,
                    'custom_timelines_duration'    => $item->custom_timelines_duration,
                    'schedule_custom_notify'       => $item->schedule_custom_notify,
                ];
            });
        }

        switch ($this->media_type) {
            case 'image':
                $media = $this->media;
                break;
            case 'embedded':
                $media = $this->getRawOriginal('media');
                break;
            default:
                $media = $this->media;
                break;
        }

        $is_redeemed = 'yes';
        $organizationCheck = isset($this->organization->uuid) ? $this->organization->uuid : null;
        if ($request->has('organization_id') && $organizationCheck != null) {
            $organizationCheck = $request->organization_id;
        }

        $organizationCheck = auth()->user()->preferred_organization;
        $organization = OrganizationService::getOrganizationExistBasedOnId($organizationCheck);
        $checkChallengeRedeem = ChallengeTemplateService::checkChallengeRedeemedOrNot($this->id, $organization->id);
        if ($checkChallengeRedeem) {
            $is_redeemed = 'no';
        }

        if (!empty($this->challenge_association)) {
            foreach ($this->challenge_association as $challenge_association) {
                if ($challenge_association->lab_marketplace_id) {
                    $getLab = LabService::getLabBasedOnId($challenge_association->lab_marketplace_id);
                    $labs[$challenge_association->lab_marketplace_id] = LabListNameResource::make($getLab);
                }

                if ($challenge_association->lab_program_id) {
                    $getLabProgram = LabProgramService::getLabProgramBasedOnId($challenge_association->lab_program_id);
                    $lab_programs[$challenge_association->lab_program_id] = LabProgramListNameResource::make($getLabProgram);
                }

                if ($challenge_association->resource_module_id) {
                    $getResourceModule = ResourceModuleService::getResourceModuleBasedOnId($challenge_association->resource_module_id);
                    $resource_modules[$challenge_association->resource_module_id] = ResourceModuleListNameResource::make($getResourceModule);
                }

                if ($challenge_association->resource_collection_id) {
                    $getResourceCollection = ResourceCollectionService::getResourceCollectionBasedOnId($challenge_association->resource_collection_id);
                    $resource_collections[$challenge_association->resource_collection_id] = ResourceCollectionListNameResource::make($getResourceCollection);
                }

                if ($challenge_association->resource_group_id) {
                    $getResourceGroup = ResourceGroupService::getResourceGroupBasedOnId($challenge_association->resource_group_id);
                    $resource_groups[$challenge_association->resource_group_id] = ResourceGroupListNameResource::make($getResourceGroup);
                }
            }
        }

        return [
            'id'                            => $this->uuid,
            'language'                      => $this->language,
            'user'                          => UserService::joinName($this->user->first_name, $this->user->last_name),
            'organization_id'               => isset($this->organization->uuid) ? $this->organization->uuid : null,
            'organization'                  => isset($this->organization->title) ? $this->organization->title : null,
            'organization_slug'             => isset($this->organization->slug) ? $this->organization->slug : null,
            'hosted_by'                     => OrganizationHostResource::make($this->organization),
            'category_id'                   => $category_id,
            'category'                      => $category,
            'duration'                      => $duration,
            'duration_id'                   => $duration_id,
            'level'                         => $level,
            'level_id'                      => $level_id,
            'slug'                          => $this->slug,
            'title'                         => $this->title,
            'description_type'              => $this->description_type == '1' ? 'scorm' : 'text',
            'description'                   => $this->description,
            'scorm'                         => new ScormResource($this->scorm?->select(['uuid', 'title', 'version'])->first()),
            'privacy'                       => ($this->privacy == '1') ? 'yes' : 'no',
            'media_type'                    => $this->media_type,
            'media'                         => $media,
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
            'participation_achievement'     => $achievement,
            'incentive_achievement'         => $incentive_achievement,
            'challenge_requirements'        => $challenge_requirements,
            'host_id'                       => $hosts,
            'challenge_assessment_criteria' => $challenge_assessment_criteria,
            'challenge_assessment'          => $challenge_assessment,
            'challenge_timelines'           => $challenge_timelines,
            'challenge_custom_timelines'    => $challenge_custom_timelines,
            'challenge_template'            => $this->challenge_project_template,
            'external_links'                => ChallengeTemplateExternalLinkResource::collection($this->external_links),
            'is_redeemed'                   => $is_redeemed,
            'credit_score'                  => '1',
            'lab_count'                     => count($labs),
            'lab_program_count'             => count($lab_programs),
            'resource_module_count'         => count($resource_modules),
            'resource_collection_count'     => count($resource_collections),
            'resource_group_count'          => count($resource_groups),
            'labs'                          => $labs,
            'lab_programs'                  => $lab_programs,
            'resource_modules'              => $resource_modules,
            'resource_collections'          => $resource_collections,
            'resource_groups'               => $resource_groups,
        ];
    }
}
