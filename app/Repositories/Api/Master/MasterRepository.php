<?php

namespace App\Repositories\Api\Master;

use App\Models\AchievementConditionList;
use App\Models\FlexibleExpireDateDuration;
use App\Models\Host;
use App\Models\LabCondition;
use App\Models\PitchTemplate;
use App\Models\SocialConnect;
use App\Services\CategoryService;
use App\Services\ProjectIndustryService;
use App\Services\ProjectStageService;
use App\Services\ProjectStatusService;
use App\Services\ProjectTypeService;
use App\Services\ProjectVerticalService;
use App\Services\SkillService;
use App\Services\SocialLinkService;
use App\Services\TagService;
use App\Services\SkillGroupService;
use App\Services\SkillStackService;
use App\Services\RankService;
use App\Services\ProjectSubmissionRequirementService;

class MasterRepository implements MasterInterface
{
    private $categoryService;
    private $skillService;
    private $tagService;
    private $projectIndustryService;
    private $projectTypeService;
    private $projectStageService;
    private $projectVerticalService;
    private $projectStatusService;
    private $socialLinkService;
    private $skillGroupService;
    private $skillStackService;
    private $rankService;
    private $projectSubmissionRequirements;
    private $achievement_condition_list;
    private $host;
    private $flexible_expireDate_duration;
    private $pitch_template;
    private $lab_condition;
    private $social_connect;

    public function __construct(CategoryService $categoryService, SkillService $skillService, TagService $tagService, ProjectIndustryService $projectIndustryService, ProjectTypeService $projectTypeService, ProjectStageService $projectStageService, ProjectVerticalService $projectVerticalService, ProjectStatusService $projectStatusService, SocialLinkService $socialLinkService, SkillGroupService $skillGroupService, SkillStackService $skillStackService, RankService $rankService, ProjectSubmissionRequirementService $projectSubmissionRequirements, AchievementConditionList $achievement_condition_list, Host $host, FlexibleExpireDateDuration $flexible_expireDate_duration, PitchTemplate $pitch_template, LabCondition $lab_condition, SocialConnect $social_connect)
    {
        $this->categoryService = $categoryService;
        $this->skillService = $skillService;
        $this->tagService = $tagService;
        $this->projectIndustryService = $projectIndustryService;
        $this->projectTypeService = $projectTypeService;
        $this->projectStageService = $projectStageService;
        $this->projectVerticalService = $projectVerticalService;
        $this->projectStatusService = $projectStatusService;
        $this->socialLinkService = $socialLinkService;
        $this->skillGroupService = $skillGroupService;
        $this->skillStackService = $skillStackService;
        $this->rankService = $rankService;
        $this->projectSubmissionRequirements = $projectSubmissionRequirements;
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
            return $this->skillService->getSkills($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTags($request)
    {
        try {
            return $this->tagService->getTags($request->language, $request->search);
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
            return $this->projectTypeService->getProjectTypes($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getStages($request)
    {
        try {
            return $this->projectStageService->getProjectStages($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getVerticals($request)
    {
        try {
            return $this->projectVerticalService->getProjectVerticals($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getStatus($request)
    {
        try {
            return $this->projectStatusService->getProjectStatus($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getSocialLinks($request)
    {
        try {
            return $this->socialLinkService->getSocialLinks($request->language, $request->search);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getSkillGroups($request)
    {
        try {
            return $this->skillGroupService->getSkillGroups($request->language, $request->search, $request->skill_stacks, $request->skills);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getSkillStacks($request)
    {
        try {
            return $this->skillStackService->getSkillStacks($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getRanks($request)
    {
        try {
            return $this->rankService->getRanks($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getProjectSubmissionRequirements($request)
    {
        try {
            return $this->projectSubmissionRequirements->getProjectSubmissionRequirements($request->language, $request->search);
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
