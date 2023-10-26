<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Services\Manage\ChallengeAchievementService;
use App\Services\Manage\ChallengeAssessmentCriteriaService;
use App\Services\Manage\ChallengeAssessmentService;
use App\Services\Manage\ChallengeCustomTimelinesService;
use App\Services\Manage\ChallengeExternalLinkService;
use App\Services\Manage\ChallengeProjectTemplateService;
use App\Services\Manage\ChallengeRequirementService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeSkillsGroupsStackService;
use App\Services\Manage\ChallengeSponsorService;
use App\Services\Manage\ChallengeTagsGroupsService;
use App\Services\Manage\ChallengeTimelinesService;
use Exception;
use Illuminate\Support\Facades\DB;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;
    private $challengeAchievementService;
    private $challengeSponsorService;
    private $challengeSkillsGroupsStackService;
    private $challengeTagsGroupsService;
    private $challengeRequirementService;
    private $challengeAssessmentCriteriaService;
    private $challengeProjectTemplateService;
    private $challengeAssessmentService;
    private $challengeTimelinesService;
    private $challengeCustomTimelinesService;
    private $challengeExternalLinkService;

    public function __construct(ChallengeService $challengeService, ChallengeAchievementService $challengeAchievementService, ChallengeSponsorService $challengeSponsorService, ChallengeSkillsGroupsStackService $challengeSkillsGroupsStackService, ChallengeTagsGroupsService $challengeTagsGroupsService, ChallengeRequirementService $challengeRequirementService, ChallengeAssessmentCriteriaService $challengeAssessmentCriteriaService, ChallengeProjectTemplateService $challengeProjectTemplateService, ChallengeAssessmentService $challengeAssessmentService, ChallengeTimelinesService $challengeTimelinesService, ChallengeCustomTimelinesService $challengeCustomTimelinesService, ChallengeExternalLinkService $challengeExternalLinkService)
    {
        $this->challengeService = $challengeService;
        $this->challengeAchievementService = $challengeAchievementService;
        $this->challengeSponsorService = $challengeSponsorService;
        $this->challengeSkillsGroupsStackService = $challengeSkillsGroupsStackService;
        $this->challengeTagsGroupsService = $challengeTagsGroupsService;
        $this->challengeRequirementService = $challengeRequirementService;
        $this->challengeAssessmentCriteriaService = $challengeAssessmentCriteriaService;
        $this->challengeProjectTemplateService = $challengeProjectTemplateService;
        $this->challengeAssessmentService = $challengeAssessmentService;
        $this->challengeTimelinesService = $challengeTimelinesService;
        $this->challengeCustomTimelinesService = $challengeCustomTimelinesService;
        $this->challengeExternalLinkService = $challengeExternalLinkService;
    }

    public function getChallengeList($request, $organization)
    {
        try {
            return $this->challengeService->getChallengeList($request, $organization);
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadChallengeCoverImage($image)
    {
        try {
            return $this->challengeService->uploadChallengeCoverImage($image);
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadChallengeAssessment($attachment)
    {
        try {
            return $this->challengeAssessmentService->uploadChallengeAssessment($attachment);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallenge($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment)
    {
        try {
            $createChallenge = DB::transaction(function () use ($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment) {
                $createChallenge = $this->challengeService->createChallenge($request, $upload_cover_image);
                $createChallengeAchievement = $this->challengeAchievementService->createChallengeAchievement($request, $createChallenge->id, $upload_achievement_image);
                $createChallengeSponsor = $this->challengeSponsorService->createChallengeSponsor($request, $createChallenge->id);
                $createChallengeSkillsGroupsStack = $this->challengeSkillsGroupsStackService->createChallengeSkillsGroupsStack($request, $createChallenge->id);
                $createChallengeTagsGroups = $this->challengeTagsGroupsService->createChallengeTagsGroups($request, $createChallenge->id);
                $createChallengeRequirement = $this->challengeRequirementService->createChallengeRequirement($request, $createChallenge->id);
                $createChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->createChallengeAssessmentCriteria($request, $createChallenge->id);
                $createChallengeAssessment = $this->challengeAssessmentService->createChallengeAssessment($request, $createChallenge->id, $upload_assessment_attachment);
                $createChallengeProjectTemplate = $this->challengeProjectTemplateService->createChallengeProjectTemplate($request, $createChallenge->id);
                $createChallengeTimelines = $this->challengeTimelinesService->createChallengeTimelines($request, $createChallenge->id);
                $createChallengeCustomTimelines = $this->challengeCustomTimelinesService->createChallengeCustomTimelines($request, $createChallenge->id);
                $createChallengeExternalLink = $this->challengeExternalLinkService->createChallengeExternalLink($request, $createChallenge->id);

                return [
                    'createChallenge'                   => $createChallenge,
                    'createChallengeAchievement'        => $createChallengeAchievement,
                    'createChallengeSponsor'            => $createChallengeSponsor,
                    'createChallengeSkillsGroupsStack'  => $createChallengeSkillsGroupsStack,
                    'createChallengeTagsGroups'         => $createChallengeTagsGroups,
                    'createChallengeRequirement'        => $createChallengeRequirement,
                    'createChallengeAssessmentCriteria' => $createChallengeAssessmentCriteria,
                    'createChallengeAssessment'         => $createChallengeAssessment,
                    'createChallengeProjectTemplate'    => $createChallengeProjectTemplate,
                    'createChallengeTimelines'          => $createChallengeTimelines,
                    'createChallengeCustomTimelines'    => $createChallengeCustomTimelines,
                    'createChallengeExternalLink'       => $createChallengeExternalLink,
                ];
            });

            if (
                $createChallenge['createChallenge'] &&
                $createChallenge['createChallengeAchievement'] &&
                $createChallenge['createChallengeSponsor'] &&
                $createChallenge['createChallengeSkillsGroupsStack'] &&
                $createChallenge['createChallengeTagsGroups'] &&
                $createChallenge['createChallengeRequirement'] &&
                $createChallenge['createChallengeAssessmentCriteria'] &&
                $createChallenge['createChallengeAssessment'] &&
                $createChallenge['createChallengeProjectTemplate'] &&
                $createChallenge['createChallengeTimelines'] &&
                $createChallenge['createChallengeCustomTimelines'] &&
                $createChallenge['createChallengeExternalLink']
            ) {
                DB::commit();

                return $createChallenge['createChallenge'];
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadChallengeParticipationAchievementImage($image)
    {
        try {
            return $this->challengeAchievementService->uploadChallengeParticipationAchievementImage($image);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeSponsor($request, $challenge)
    {
        try {
            return $this->challengeSponsorService->createChallengeSponsor($request, $challenge);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeSkillsGroupsStack($request, $challenge)
    {
        try {
            return $this->challengeSkillsGroupsStackService->createChallengeSkillsGroupsStack($request, $challenge);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeTagsGroups($request, $challenge)
    {
        try {
            return $this->challengeTagsGroupsService->createChallengeTagsGroups($request, $challenge);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeRequirement($request, $challenge)
    {
        try {
            return $this->challengeRequirementService->createChallengeRequirement($request, $challenge);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeAssessmentCriteria($request, $challenge)
    {
        try {
            return $this->challengeAssessmentCriteriaService->createChallengeAssessmentCriteria($request, $challenge);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeAssessment($request, $challenge)
    {
        try {
            return $this->challengeAssessmentService->createChallengeAssessment($request, $challenge);
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengeProjectTemplate($request, $challenge)
    {
        try {
            return $this->challengeProjectTemplateService->createChallengeProjectTemplate($request, $challenge);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateChallenge($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment)
    {
        try {
            $updateChallenge = DB::transaction(function () use ($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment) {
                $updateChallenge = $this->challengeService->updateChallenge($slug, $request, $update_cover_image);
                $updateChallengeAchievement = $this->challengeAchievementService->updateChallengeAchievement($updateChallenge->id, $request, $update_participation_achievement_image);
                $updateChallengeSponsor = $this->challengeSponsorService->updateChallengeSponsor($updateChallenge->id, $request);
                $updateChallengeSkillsGroupsStack = $this->challengeSkillsGroupsStackService->updateChallengeSkillsGroupsStack($request, $updateChallenge->id);
                $updateChallengeTagsGroups = $this->challengeTagsGroupsService->updateChallengeTagsGroups($request, $updateChallenge->id);
                $updateChallengeRequirement = $this->challengeRequirementService->updateChallengeRequirement($request, $updateChallenge->id);
                $updateChallengeAssessmentCriteria = $this->challengeAssessmentCriteriaService->updateChallengeAssessmentCriteria($request, $updateChallenge->id);
                $updateChallengeAssessment = $this->challengeAssessmentService->updateChallengeAssessment($request, $updateChallenge->id, $update_assessment_attachment);
                $updateChallengeProjectTemplate = $this->challengeProjectTemplateService->updateChallengeProjectTemplate($request, $updateChallenge->id);
                $updateChallengeTimelines = $this->challengeTimelinesService->updateChallengeTimelines($request, $updateChallenge->id);
                $updateChallengeCustomTimelines = $this->challengeCustomTimelinesService->updateChallengeCustomTimelines($request, $updateChallenge->id);
                $updateChallengeExternalLinks = $this->challengeExternalLinkService->updateChallengeExternalLink($request, $updateChallenge->id);

                return [
                    'updateChallenge'                   => $updateChallenge,
                    'updateChallengeAchievement'        => $updateChallengeAchievement,
                    'updateChallengeSponsor'            => $updateChallengeSponsor,
                    'updateChallengeSkillsGroupsStack'  => $updateChallengeSkillsGroupsStack,
                    'updateChallengeTagsGroups'         => $updateChallengeTagsGroups,
                    'updateChallengeRequirement'        => $updateChallengeRequirement,
                    'updateChallengeAssessmentCriteria' => $updateChallengeAssessmentCriteria,
                    'updateChallengeAssessment'         => $updateChallengeAssessment,
                    'updateChallengeProjectTemplate'    => $updateChallengeProjectTemplate,
                    'updateChallengeTimelines'          => $updateChallengeTimelines,
                    'updateChallengeCustomTimelines'    => $updateChallengeCustomTimelines,
                    'updateChallengeExternalLinks'      => $updateChallengeExternalLinks,
                ];
            });

            if (
                $updateChallenge['updateChallenge'] &&
                $updateChallenge['updateChallengeAchievement'] &&
                $updateChallenge['updateChallengeSponsor'] &&
                $updateChallenge['updateChallengeSkillsGroupsStack'] &&
                $updateChallenge['updateChallengeTagsGroups'] &&
                $updateChallenge['updateChallengeRequirement'] &&
                $updateChallenge['updateChallengeAssessmentCriteria'] &&
                $updateChallenge['updateChallengeAssessment'] &&
                $updateChallenge['updateChallengeProjectTemplate'] &&
                $updateChallenge['updateChallengeTimelines'] &&
                $updateChallenge['updateChallengeCustomTimelines'] &&
                $updateChallenge['updateChallengeExternalLinks']
            ) {
                DB::commit();

                return $updateChallenge['updateChallenge'];
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getChallengeBasedOnSlug($slug)
    {
        try {
            return $this->challengeService->getChallengeBasedOnSlug($slug);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteChallenge($lab_id, $request)
    {
        try {
            DB::beginTransaction();

            $deleteChallenge = $this->challengeService->deleteChallenge($lab_id);
            if ($deleteChallenge == false) {
                DB::rollBack();

                return false;
            }
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function checkSlug($slug)
    {
        try {
            return $this->challengeService->getChallengeBasedOnSlug($slug);
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $labSlug = $this->challengeService->checkNameExistsOrNot($title);

            return $labSlug;
        } catch (Exception $e) {
            return false;
        }
    }
}
