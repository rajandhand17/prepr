<?php

namespace App\Traits\Maestro\ChallengePathTemplate;

use App\Helpers\UtilityHelper;
use App\Services\Maestro\ChallengePathTemplateAchievementsService;
use App\Services\Maestro\ChallengePathTemplateService;
use App\Services\Maestro\ChallengePathTemplateSkillsGroupsStackService;
use App\Services\Maestro\ChallengePathTemplateTagsGroupsService;
use Illuminate\Support\Facades\DB;

trait ChallengePathTemplateTrait
{
    public function addChallengePathToTemplate($challengePathId)
    {
        try {
            $addChallengePathTemplate = DB::transaction(function () use ($challengePathId) {
                $addChallengePathTemplate = ChallengePathTemplateService::addChallengePathTemplate($challengePathId);
                $addChallengePathTemplateAchievement = ChallengePathTemplateAchievementsService::addChallengePathTemplateAchievement($challengePathId, $addChallengePathTemplate->id);
                $addChallengePathTemplateSkillsGroupsStack = ChallengePathTemplateSkillsGroupsStackService::addChallengePathTemplateSkillsGroupsStack($challengePathId, $addChallengePathTemplate->id);
                $addChallengePathTemplateTagsGroupsService = ChallengePathTemplateTagsGroupsService::addChallengePathTemplateTagsGroupsService($challengePathId, $addChallengePathTemplate->id);

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
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
