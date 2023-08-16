<?php

namespace App\Repositories\Api\Master;

use App\Models\AchievementConditionList;
use App\Models\FlexibleExpireDateDuration;
use App\Models\Host;
use App\Models\LabCondition;
use App\Models\PitchTemplate;
use App\Models\ProjectStage;
use App\Models\ProjectStatus;
use App\Models\ProjectSubmissionRequirement;
use App\Models\ProjectType;
use App\Models\ProjectVertical;
use App\Models\Rank;
use App\Models\Skill;
use App\Models\SkillGroup;
use App\Models\SkillStack;
use App\Models\SocialConnect;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Services\CategoryService;
use App\Services\ProjectIndustryService;

class MasterRepository implements MasterInterface
{
    private $categoryService;
    private $skill;
    private $tag;
    private $projectIndustryService;
    private $project_type;
    private $project_stage;
    private $project_verticals;
    private $project_status;
    private $social_link;
    private $skill_group;
    private $skill_stack;
    private $rank;
    private $project_submission_requirements;
    private $achievement_condition_list;
    private $host;
    private $flexible_expireDate_duration;
    private $pitch_template;
    private $lab_condition;
    private $social_connect;

    public function __construct(CategoryService $categoryService, Skill $skill, Tag $tag, ProjectIndustryService $projectIndustryService, ProjectType $project_type, ProjectStage $project_stage, ProjectVertical $project_verticals, ProjectStatus $project_status, SocialLink $social_link, SkillGroup $skill_group, SkillStack $skill_stack, Rank $rank, ProjectSubmissionRequirement $project_submission_requirements, AchievementConditionList $achievement_condition_list, Host $host, FlexibleExpireDateDuration $flexible_expireDate_duration, PitchTemplate $pitch_template, LabCondition $lab_condition, SocialConnect $social_connect)
    {
        $this->categoryService = $categoryService;
        $this->skill = $skill;
        $this->tag = $tag;
        $this->projectIndustryService = $projectIndustryService;
        $this->project_type = $project_type;
        $this->project_stage = $project_stage;
        $this->project_verticals = $project_verticals;
        $this->project_status = $project_status;
        $this->social_link = $social_link;
        $this->skill_group = $skill_group;
        $this->skill_stack = $skill_stack;
        $this->rank = $rank;
        $this->project_submission_requirements = $project_submission_requirements;
        $this->achievement_condition_list = $achievement_condition_list;
        $this->host = $host;
        $this->flexible_expireDate_duration = $flexible_expireDate_duration;
        $this->pitch_template = $pitch_template;
        $this->lab_condition = $lab_condition;
        $this->social_connect = $social_connect;
    }

    public function getCategories($request)
    {
        try {
            return $this->categoryService->getCategories($request->language, $request->search, $request->component);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getSkills($request)
    {
        try {
            return $this->skill->getSkills($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTags($request)
    {
        try {
            return $this->tag->getTags($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getProjectIndustries($request)
    {
        try {
            return $this->projectIndustryService->getProjectIndustries($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getProjectTypes($request)
    {
        try {
            return $this->project_type->getProjectTypes($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getStages($request)
    {
        try {
            return $this->project_stage->getProjectStages($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getVerticals($request)
    {
        try {
            return $this->project_verticals->getProjectVerticals($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getStatus($request)
    {
        try {
            return $this->project_status->getProjectStatus($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getSocialLinks($request)
    {
        try {
            return $this->social_link->getSocialLinks($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getSkillGroups($request)
    {
        try {
            return $this->skill_group->getSkillGroups($request->language, $request->search, $request->skill_stacks, $request->skills);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getSkillStacks($request)
    {
        try {
            return $this->skill_stack->getSkillStacks($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getRanks($request)
    {
        try {
            return $this->rank->getRanks($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getProjectSubmissionRequirements($request)
    {
        try {
            return $this->project_submission_requirements->getProjectSubmissionRequirements($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getAchievementConditionLists($request)
    {
        try {
            return $this->achievement_condition_list->getAchievementConditionLists($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getHosts($request)
    {
        try {
            return $this->host->getHosts($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getFlexibleDateDurations($request)
    {
        try {
            return $this->flexible_expireDate_duration->getFlexibleDateDurations($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getPitchTemplates($request)
    {
        try {
            return $this->pitch_template->getPitchTemplates($request->language, $request->search);
        } catch (\Exception) {
            return false;
        }
    }

    public function getLabConditions($request)
    {
        try {
            return $this->lab_condition->getLabConditions($request->language, $request->search);
        } catch (\Exception) {
            return false;
        }
    }

    public function getSocialConnect($request)
    {
        try {
            return $this->social_connect->getSocialCon0nect($request->language, $request->search);
        } catch (\Exception) {
            return false;
        }
    }
}
