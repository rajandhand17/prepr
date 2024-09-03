<?php

namespace App\Repositories\Api\Manage\ChallengePathTemplate;

use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengePathTemplateAchievementsService;
use App\Services\Manage\ChallengePathTemplateService;
use App\Services\Manage\ChallengePathTemplateSkillsGroupsStackService;
use App\Services\Manage\ChallengePathTemplateTagsGroupsService;
use Exception;
use Illuminate\Support\Facades\DB;

class ChallengePathTemplateRepository implements ChallengePathTemplateInterface
{
    private $challengePathTemplateService;
    private $challengePathTemplateSkillsGroupsStackService;
    private $challengePathTemplateAchievementsService;
    private $challengePathTemplateTagsGroupsService;

    public function __construct(ChallengePathTemplateService $challengePathTemplateService, ChallengePathTemplateAchievementsService $challengePathTemplateAchievementsService, ChallengePathTemplateSkillsGroupsStackService $challengePathTemplateSkillsGroupsStackService, ChallengePathTemplateTagsGroupsService $challengePathTemplateTagsGroupsService)
    {
        $this->challengePathTemplateService = $challengePathTemplateService;
        $this->challengePathTemplateSkillsGroupsStackService = $challengePathTemplateSkillsGroupsStackService;
        $this->challengePathTemplateAchievementsService = $challengePathTemplateAchievementsService;
        $this->challengePathTemplateTagsGroupsService = $challengePathTemplateTagsGroupsService;
    }

    public function addChallengePathToTemplate($challengePathId)
    {
        try {
            $addChallengePathTemplate = DB::transaction(function () use ($challengePathId) {
                $addChallengePathTemplate = $this->challengePathTemplateService->addChallengePathTemplate($challengePathId);
                $addChallengePathTemplateAchievement = $this->challengePathTemplateAchievementsService->addChallengePathTemplateAchievement($challengePathId, $addChallengePathTemplate->id);
                $addChallengePathTemplateSkillsGroupsStack = $this->challengePathTemplateSkillsGroupsStackService->addChallengePathTemplateSkillsGroupsStack($challengePathId, $addChallengePathTemplate->id);
                $addChallengePathTemplateTagsGroupsService = $this->challengePathTemplateTagsGroupsService->addChallengePathTemplateTagsGroupsService($challengePathId, $addChallengePathTemplate->id);

                return [
                    'addChallengePathTemplate'                     => $addChallengePathTemplate,
                    'addChallengePathTemplateAchievement'          => $addChallengePathTemplateAchievement,
                    'addChallengePathTemplateSkillsGroupsStack'    => $addChallengePathTemplateSkillsGroupsStack,
                    'addChallengePathTemplateTagsGroupsService'    => $addChallengePathTemplateTagsGroupsService,
                ];
            });

            if (
                $addChallengePathTemplate['addChallengePathTemplate'] &&
                $addChallengePathTemplate['addChallengePathTemplateAchievement'] &&
                $addChallengePathTemplate['addChallengePathTemplateSkillsGroupsStack'] &&
                $addChallengePathTemplate['addChallengePathTemplateTagsGroupsService']
            ) {
                DB::commit();

                return $addChallengePathTemplate['addChallengePathTemplate'];
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function redeemChallengePath($challengePathTemplateId, $organizationId)
    {
        try {
            $redeemChallengePathTemplate = DB::transaction(function () use ($challengePathTemplateId, $organizationId) {
                $redeemChallengePathTemplateToChallengePath = $this->challengePathTemplateService->redeemChallengePathTemplateToChallengePath($challengePathTemplateId, $organizationId);
                $redeemChallengePathTemplateToChallengePathAchievement = $this->challengePathTemplateAchievementsService->redeemChallengePathTemplateToChallengePathAchievement($challengePathTemplateId, $redeemChallengePathTemplateToChallengePath->id);
                $redeemChallengePathTemplateToChallengePathSkillsGroupsStack = $this->challengePathTemplateSkillsGroupsStackService->redeemChallengePathTemplateToChallengePathSkillsGroupsStack($challengePathTemplateId, $redeemChallengePathTemplateToChallengePath->id);
                $redeemChallengePathTemplateToChallengePathTagsGroupsService = $this->challengePathTemplateTagsGroupsService->redeemChallengePathTemplateToChallengePathTagsGroupsService($challengePathTemplateId, $redeemChallengePathTemplateToChallengePath->id);

                return [
                    'redeemChallengePathTemplateToChallengePath'                    => $redeemChallengePathTemplateToChallengePath,
                    'redeemChallengePathTemplateToChallengePathAchievement'         => $redeemChallengePathTemplateToChallengePathAchievement,
                    'redeemChallengePathTemplateToChallengePathSkillsGroupsStack'   => $redeemChallengePathTemplateToChallengePathSkillsGroupsStack,
                    'redeemChallengePathTemplateToChallengePathTagsGroupsService'   => $redeemChallengePathTemplateToChallengePathTagsGroupsService,
                ];
            });

            if (
                $redeemChallengePathTemplate['redeemChallengePathTemplateToChallengePath'] &&
                $redeemChallengePathTemplate['redeemChallengePathTemplateToChallengePathAchievement'] &&
                $redeemChallengePathTemplate['redeemChallengePathTemplateToChallengePathSkillsGroupsStack'] &&
                $redeemChallengePathTemplate['redeemChallengePathTemplateToChallengePathTagsGroupsService']
            ) {
                DB::commit();

                return $redeemChallengePathTemplate['redeemChallengePathTemplateToChallengePath'];
            }
            DB::rollBack();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
