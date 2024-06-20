<?php

namespace App\Repositories\Api\Master;

use App\Services\AchievementConditionListService;
use App\Services\BusinessChallengeTacklingService;
use App\Services\CategoryService;
use App\Services\ChallengeAnnouncementRecipientService;
use App\Services\CountryService;
use App\Services\DurationService;
use App\Services\FlexibleExpireDateDurationService;
use App\Services\HostService;
use App\Services\JobTitleService;
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
use App\Services\TagGroupService;
use App\Services\TagService;
use Exception;
use Illuminate\Support\Facades\Log;

class MasterRepository implements MasterInterface
{
    private $categoryService;

    private $countryService;
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
    private $tagGroupService;
    private $challengeAnnouncementRecipientService;
    private $jobTitleService;
    private $businessChallengeTacklingService;

    public function __construct(CountryService $countryService, CategoryService $categoryService, SkillService $skillService, TagService $tagService, ProjectIndustryService $projectIndustryService, ProjectTypeService $projectTypeService, ProjectStageService $projectStageService, ProjectVerticalService $projectVerticalService, ProjectStatusService $projectStatusService, SocialLinkService $socialLinkService, SkillGroupService $skillGroupService, SkillStackService $skillStackService, RankService $rankService, ProjectSubmissionRequirementService $projectSubmissionRequirements, AchievementConditionListService $achievementConditionListService, HostService $hostService, FlexibleExpireDateDurationService $flexibleExpireDateDurationService, PitchTemplateService $pitchTemplateService, LabConditionService $labConditionService, SocialConnectService $socialConnectService, DurationService $durationService, LevelService $levelService, ChallengeAnnouncementRecipientService $challengeAnnouncementRecipientService, TagGroupService $tagGroupService, JobTitleService $jobTitleService, BusinessChallengeTacklingService $businessChallengeTacklingService)
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
        $this->durationService = $durationService;
        $this->levelService = $levelService;
        $this->tagGroupService = $tagGroupService;
        $this->challengeAnnouncementRecipientService = $challengeAnnouncementRecipientService;
        $this->countryService = $countryService;
        $this->jobTitleService = $jobTitleService;
        $this->businessChallengeTacklingService = $businessChallengeTacklingService;
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
            return $this->skillService->getSkills($request->language, $request->search, $sortBy = null, $skill_id = null, $pagination = null);
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

    public function getDurations($request)
    {
        try {
            return $this->durationService->getDurations($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLevels($request)
    {
        try {
            return $this->levelService->getLevels($request->language, $request->search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getPitchTaskData($request)
    {
        try {
            return $this->pitchTemplateService->getPitchTemplatesBasedOnId($request->template_id);
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkSponsor($request)
    {
        try {
            return $this->hostService->checkSponsor($request);
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadSponsorMedia($image)
    {
        try {
            return $this->hostService->uploadSponsorMedia($image);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createSponsor($request, $upload_sponsor_image)
    {
        try {
            return $this->hostService->createSponsor($request, $upload_sponsor_image);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getChallengeAnnouncementRecipient($request)
    {
        try {
            return $this->challengeAnnouncementRecipientService->getChallengeAnnouncementRecipient($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTagGroups($request)
    {
        try {
            return $this->tagGroupService->getTagGroups($request->language, $request->search, $request->skills);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCountries($request)
    {
        try {
            return $this->countryService->getCountries($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getJobTitles($request)
    {
        try {
            return $this->jobTitleService->getJobTitles($request->language, $request->search);
        } catch (Exception $e) {
            Log::error('Error in getJobTitles in MasterRepository.php: '.$e->getMessage());

            return false;
        }
    }

    public function getBusinessChallengeTackling($request)
    {
        try {
            return $this->businessChallengeTacklingService->getBusinessChallengeTackling($request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
