<?php

namespace App\Repositories\Api\Manage\ChallengeTemplate;

use App\Helpers\UtilityHelper;
use App\Models\LabChallengeRedeem;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeTemplateAchievementService;
use App\Services\Manage\ChallengeTemplateAssessmentCriteriaService;
use App\Services\Manage\ChallengeTemplateAssessmentService;
use App\Services\Manage\ChallengeTemplateCustomTimelinesService;
use App\Services\Manage\ChallengeTemplateExternalLinkService;
use App\Services\Manage\ChallengeTemplateProjectTemplateService;
use App\Services\Manage\ChallengeTemplateRequirementService;
use App\Services\Manage\ChallengeTemplateService;
use App\Services\Manage\ChallengeTemplateSkillsGroupsStackService;
use App\Services\Manage\ChallengeTemplateSponsorService;
use App\Services\Manage\ChallengeTemplateTagsGroupsService;
use App\Services\Manage\ChallengeTemplateTimelinesService;
use Exception;
use Illuminate\Support\Facades\DB;

class ChallengeTemplateRepository implements ChallengeTemplateInterface
{
    private $challengeTemplateService;

    private $challengeTemplateAchievementService;

    private $challengeTemplateSkillsGroupsStackService;

    private $challengeTemplateSponsorService;

    private $challengeTemplateTagsGroupsService;

    private $challengeTemplateRequirementService;

    private $challengeTemplateAssessmentCriteriaService;

    private $challengeTemplateAssessmentService;

    private $challengeTemplateProjectTemplateService;

    private $challengeTemplateTimelinesService;

    private $challengeTemplateCustomTimelinesService;

    private $challengeTemplateExternalLinkService;

    private $challengeService;

    public function __construct(ChallengeTemplateExternalLinkService $challengeTemplateExternalLinkService, ChallengeTemplateCustomTimelinesService $challengeTemplateCustomTimelinesService, ChallengeTemplateTimelinesService $challengeTemplateTimelinesService, ChallengeTemplateProjectTemplateService $challengeTemplateProjectTemplateService, ChallengeTemplateAssessmentService $challengeTemplateAssessmentService, ChallengeTemplateAssessmentCriteriaService $challengeTemplateAssessmentCriteriaService, ChallengeTemplateRequirementService $challengeTemplateRequirementService, ChallengeTemplateTagsGroupsService $challengeTemplateTagsGroupsService, ChallengeTemplateSponsorService $challengeTemplateSponsorService, ChallengeTemplateSkillsGroupsStackService $challengeTemplateSkillsGroupsStackService, ChallengeTemplateService $challengeTemplateService, ChallengeTemplateAchievementService $challengeTemplateAchievementService, ChallengeService $challengeService)
    {
        $this->challengeTemplateService = $challengeTemplateService;
        $this->challengeTemplateAchievementService = $challengeTemplateAchievementService;
        $this->challengeTemplateSkillsGroupsStackService = $challengeTemplateSkillsGroupsStackService;
        $this->challengeTemplateSponsorService = $challengeTemplateSponsorService;
        $this->challengeTemplateTagsGroupsService = $challengeTemplateTagsGroupsService;
        $this->challengeTemplateRequirementService = $challengeTemplateRequirementService;
        $this->challengeTemplateAssessmentService = $challengeTemplateAssessmentService;
        $this->challengeTemplateProjectTemplateService = $challengeTemplateProjectTemplateService;
        $this->challengeTemplateAssessmentCriteriaService = $challengeTemplateAssessmentCriteriaService;
        $this->challengeTemplateTimelinesService = $challengeTemplateTimelinesService;
        $this->challengeTemplateCustomTimelinesService = $challengeTemplateCustomTimelinesService;
        $this->challengeTemplateExternalLinkService = $challengeTemplateExternalLinkService;
        $this->challengeService = $challengeService;
    }

    public function getChallengeTemplateList($request)
    {
        try {
            return $this->challengeTemplateService->getChallengeTemplateList($request);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getCheckChallengeUuid($uuid)
    {
        try {
            return $this->challengeTemplateService->getCheckChallengeUuid($uuid);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addChallengeToTemplate($challengeId)
    {
        try {
            $addChallengeToTemplate = DB::transaction(function () use ($challengeId) {
                $addChallengeTemplate = $this->challengeTemplateService->addChallengeTemplate($challengeId);
                $addChallengeTemplateParticipationAchievement = $this->challengeTemplateAchievementService->addChallengeTemplateAchievement($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateSkills = $this->challengeTemplateSkillsGroupsStackService->addChallengeTemplateSkills($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateSponsor = $this->challengeTemplateSponsorService->addChallengeTemplateSponsor($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateTags = $this->challengeTemplateTagsGroupsService->addChallengeTemplateTagsGroups($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateRequirement = $this->challengeTemplateRequirementService->addChallengeTemplateRequirement($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateAssessment = $this->challengeTemplateAssessmentService->addChallengeTemplateAssessment($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateAssessmentCriteria = $this->challengeTemplateAssessmentCriteriaService->addChallengeTemplateAssessmentCriteria($challengeId, $addChallengeTemplate->id, $addChallengeTemplateAssessment);
                $addChallengeTemplateProjectTemplate = $this->challengeTemplateProjectTemplateService->addChallengeTemplateProjectTemplate($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateTimelines = $this->challengeTemplateTimelinesService->addChallengeTemplateTimelines($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateCustomTimelines = $this->challengeTemplateCustomTimelinesService->addChallengeTemplateCustomTimeLines($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateExternalLink = $this->challengeTemplateExternalLinkService->addChallengeTemplateExternalLink($challengeId, $addChallengeTemplate->id);
                $addChallengeTemplateComponentAssociation = $this->challengeTemplateService->addChallengeTemplateComponentAssociation($challengeId, $addChallengeTemplate->id);
                $updateChallenge = $this->challengeService->updatePreBuilt($challengeId, '1');

                return [
                    'addChallengeTemplate'                              => $addChallengeTemplate,
                    'addChallengeTemplateParticipationAchievement'      => $addChallengeTemplateParticipationAchievement,
                    'addChallengeTemplateSkills'                        => $addChallengeTemplateSkills,
                    'addChallengeTemplateSponsor'                       => $addChallengeTemplateSponsor,
                    'addChallengeTemplateTags'                          => $addChallengeTemplateTags,
                    'addChallengeTemplateRequirement'                   => $addChallengeTemplateRequirement,
                    'addChallengeTemplateAssessmentCriteria'            => $addChallengeTemplateAssessmentCriteria,
                    'addChallengeTemplateAssessment'                    => $addChallengeTemplateAssessment,
                    'addChallengeTemplateProjectTemplate'               => $addChallengeTemplateProjectTemplate,
                    'addChallengeTemplateTimelines'                     => $addChallengeTemplateTimelines,
                    'addChallengeTemplateCustomTimelines'               => $addChallengeTemplateCustomTimelines,
                    'addChallengeTemplateExternalLink'                  => $addChallengeTemplateExternalLink,
                    'updateChallenge'                                   => $updateChallenge,
                    'addChallengeTemplateComponentAssociation'          => $addChallengeTemplateComponentAssociation,
                ];
            });

            if (
                $addChallengeToTemplate['addChallengeTemplate'] &&
                $addChallengeToTemplate['addChallengeTemplateParticipationAchievement'] &&
                $addChallengeToTemplate['addChallengeTemplateSkills'] &&
                $addChallengeToTemplate['addChallengeTemplateSponsor'] &&
                $addChallengeToTemplate['addChallengeTemplateTags'] &&
                $addChallengeToTemplate['addChallengeTemplateRequirement'] &&
                $addChallengeToTemplate['addChallengeTemplateAssessmentCriteria'] &&
                $addChallengeToTemplate['addChallengeTemplateAssessment'] &&
                $addChallengeToTemplate['addChallengeTemplateProjectTemplate'] &&
                $addChallengeToTemplate['addChallengeTemplateTimelines'] &&
                $addChallengeToTemplate['addChallengeTemplateCustomTimelines'] &&
                $addChallengeToTemplate['addChallengeTemplateExternalLink'] &&
                $addChallengeToTemplate['updateChallenge'] &&
                $addChallengeToTemplate['addChallengeTemplateComponentAssociation']
            ) {
                self::addChallengeRedeemData($challengeId, $addChallengeToTemplate['addChallengeTemplate']->organization_id, $addChallengeToTemplate['addChallengeTemplate']->id);
                DB::commit();

                return $addChallengeToTemplate['addChallengeTemplate'];
            }

            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function getChallengeTemplateBasedOnSlug($slug)
    {
        try {
            return $this->challengeTemplateService->getChallengeTemplateBasedOnSlug($slug);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function addChallengeRedeemData($challengeId, $organizationId, $challengeTemplateId)
    {
        try {
            $challengeRedeem = new LabChallengeRedeem();
            $challengeRedeem->user_id = auth()->user()->id;
            $challengeRedeem->organization_id = $organizationId;
            $challengeRedeem->lab_id = null;
            $challengeRedeem->lab_marketplace_id = null;
            $challengeRedeem->challenge_id = $challengeId;
            $challengeRedeem->challenge_template_id = $challengeTemplateId;
            $challengeRedeem->is_redeemed = '0';
            $challengeRedeem->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function checkChallengeRedeemedOrNot($challengeTemplateId, $organizationId)
    {
        try {
            return $this->challengeTemplateService->checkChallengeRedeemedOrNot($challengeTemplateId, $organizationId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function challengeRedeem($challengeTemplateId, $organizationId)
    {
        try {
            $redeemChallengeTemplate = DB::transaction(function () use ($challengeTemplateId, $organizationId) {
                $redeemChallengeTemplateToChallenge = $this->challengeTemplateService->redeemChallengeTemplateToChallenge($challengeTemplateId, $organizationId);
                $redeemChallengeTemplateAchievement = $this->challengeTemplateAchievementService->redeemChallengeTemplateAchievement($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateSkillGroupStack = $this->challengeTemplateSkillsGroupsStackService->redeemChallengeTemplateSkillGroupStack($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateTagGroup = $this->challengeTemplateTagsGroupsService->redeemChallengeTemplateTagGroup($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateSponsor = $this->challengeTemplateSponsorService->redeemChallengeTemplateSponsor($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateRequirement = $this->challengeTemplateRequirementService->redeemChallengeTemplateRequirement($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateAssessment = $this->challengeTemplateAssessmentService->redeemChallengeTemplateAssessment($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateAssessmentCriteria = $this->challengeTemplateAssessmentCriteriaService->redeemChallengeTemplateAssessmentCriteria($redeemChallengeTemplateToChallenge->id, $challengeTemplateId, $redeemChallengeTemplateAssessment);
                $redeemChallengeTemplateProjectTemplate = $this->challengeTemplateProjectTemplateService->redeemChallengeTemplateProjectTemplate($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateTimeline = $this->challengeTemplateTimelinesService->redeemChallengeTemplateTimeline($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateCustomTimelines = $this->challengeTemplateCustomTimelinesService->redeemChallengeTemplateCustomTimelines($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateExternalLink = $this->challengeTemplateExternalLinkService->redeemChallengeTemplateExternalLink($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);
                $redeemChallengeTemplateComponentAssociation = $this->challengeTemplateService->redeemChallengeTemplateComponentAssociation($redeemChallengeTemplateToChallenge->id, $challengeTemplateId);

                return [
                    'redeemChallengeTemplateToChallenge'            => $redeemChallengeTemplateToChallenge,
                    'redeemChallengeTemplateAchievement'            => $redeemChallengeTemplateAchievement,
                    'redeemChallengeTemplateSkillGroupStack'        => $redeemChallengeTemplateSkillGroupStack,
                    'redeemChallengeTemplateTagGroup'               => $redeemChallengeTemplateTagGroup,
                    'redeemChallengeTemplateSponsor'                => $redeemChallengeTemplateSponsor,
                    'redeemChallengeTemplateRequirement'            => $redeemChallengeTemplateRequirement,
                    'redeemChallengeTemplateAssessmentCriteria'     => $redeemChallengeTemplateAssessmentCriteria,
                    'redeemChallengeTemplateAssessment'             => $redeemChallengeTemplateAssessment,
                    'redeemChallengeTemplateProjectTemplate'        => $redeemChallengeTemplateProjectTemplate,
                    'redeemChallengeTemplateTimeline'               => $redeemChallengeTemplateTimeline,
                    'redeemChallengeTemplateCustomTimelines'        => $redeemChallengeTemplateCustomTimelines,
                    'redeemChallengeTemplateExternalLink'           => $redeemChallengeTemplateExternalLink,
                    'redeemChallengeTemplateComponentAssociation'   => $redeemChallengeTemplateComponentAssociation,
                ];
            });

            if (
                $redeemChallengeTemplate['redeemChallengeTemplateToChallenge'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateAchievement'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateSkillGroupStack'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateTagGroup'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateSponsor'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateRequirement'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateAssessmentCriteria'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateAssessment'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateProjectTemplate'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateTimeline'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateCustomTimelines'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateExternalLink'] &&
                $redeemChallengeTemplate['redeemChallengeTemplateComponentAssociation']
            ) {
                self::addChallengeRedeemed($challengeTemplateId, $redeemChallengeTemplate['redeemChallengeTemplateToChallenge']->organization_id, $redeemChallengeTemplate['redeemChallengeTemplateToChallenge']->id);
                DB::commit();

                return $redeemChallengeTemplate['redeemChallengeTemplateToChallenge'];
            }

            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }

    public function addChallengeRedeemed($challengeTemplateId, $organizationId, $challengeId)
    {
        try {
            $labRedeem = new LabChallengeRedeem();
            $labRedeem->user_id = auth()->user()->id;
            $labRedeem->organization_id = $organizationId;
            $labRedeem->lab_id = null;
            $labRedeem->lab_marketplace_id = null;
            $labRedeem->challenge_id = $challengeId;
            $labRedeem->challenge_template_id = $challengeTemplateId;
            $labRedeem->is_redeemed = '1';
            $labRedeem->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function deleteChallengeTemplate($slug, $challengeTemplateId)
    {
        try {
            return $this->challengeTemplateService->deleteChallengeTemplate($slug, $challengeTemplateId);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
