<?php

namespace App\Repositories\Api\Master;

use App\Services\AchievementConditionListService;
use App\Services\CategoryService;
use App\Services\DurationService;
use App\Services\FlexibleExpireDateDurationService;
use App\Services\HostService;
use App\Services\LabConditionService;
use App\Services\LevelService;
use App\Services\PitchTemplateService;
use App\Services\ProjectIndustryService;
use App\Services\ProjectStageService;
use App\Services\ProjectStatusService;
use App\Services\ProjectSubmissionRequirementService;
use App\Services\ProjectTypeService;
use App\Services\ProjectVerticalService;
use App\Services\RankService;
use App\Services\SkillGroupService;
use App\Services\SkillService;
use App\Services\SkillStackService;
use App\Services\SocialConnectService;
use App\Services\SocialLinkService;
use App\Services\TagService;

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
    private $achievementConditionListService;
    private $hostService;
    private $flexibleExpireDateDurationService;
    private $pitchTemplateService;
    private $labConditionService;
    private $socialConnectService;

    private $durationService;

    private $levelService;

    public function __construct(CategoryService $categoryService, SkillService $skillService, TagService $tagService, ProjectIndustryService $projectIndustryService, ProjectTypeService $projectTypeService, ProjectStageService $projectStageService, ProjectVerticalService $projectVerticalService, ProjectStatusService $projectStatusService, SocialLinkService $socialLinkService, SkillGroupService $skillGroupService, SkillStackService $skillStackService, RankService $rankService, ProjectSubmissionRequirementService $projectSubmissionRequirements, AchievementConditionListService $achievementConditionListService, HostService $hostService, FlexibleExpireDateDurationService $flexibleExpireDateDurationService, PitchTemplateService $pitchTemplateService, LabConditionService $labConditionService, SocialConnectService $socialConnectService, DurationService $durationService,LevelService $levelService)
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
        $this->achievementConditionListService = $achievementConditionListService;
        $this->hostService = $hostService;
        $this->flexibleExpireDateDurationService = $flexibleExpireDateDurationService;
        $this->pitchTemplateService = $pitchTemplateService;
        $this->labConditionService = $labConditionService;
        $this->socialConnectService = $socialConnectService;
        $this->durationService=$durationService;
        $this->levelService=$levelService;
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
            return $this->achievementConditionListService->getAchievementConditionLists($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getHosts($request)
    {
        try {
            return $this->hostService->getHosts($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getFlexibleDateDurations($request)
    {
        try {
            return $this->flexibleExpireDateDurationService->getFlexibleDateDurations($request->language, $request->search);
        } catch(\Exception) {
            return false;
        }
    }

    public function getPitchTemplates($request)
    {
        try {
            return $this->pitchTemplateService->getPitchTemplates($request->language, $request->search);
        } catch (\Exception) {
            return false;
        }
    }

    public function getLabConditions($request)
    {
        try {
            return $this->labConditionService->getLabConditions($request->language, $request->search);
        } catch (\Exception) {
            return false;
        }
    }

    public function getSocialConnect($request)
    {
        try {
            return $this->socialConnectService->getSocialConnect($request->language, $request->search);
        } catch (\Exception) {
            return false;
        }
    }

    public function getDuration($request){
        try {
            return $this->durationService->getDuration($request->language, $request->search);
        }catch (\Exception $e){
            return false;
        }
    }

    public  function getLevels($request){
        try {
            return $this->levelService->getLevels($request->language, $request->search);
        }catch (\Exception $e){
            return false;
        }
    }
}
