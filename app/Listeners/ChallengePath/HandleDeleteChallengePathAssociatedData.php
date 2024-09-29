<?php

namespace App\Listeners\ChallengePath;

use App\Events\ChallengePath\DeleteChallengePathAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengePathAchievementsService;
use App\Services\Manage\ChallengePathSkillsGroupsStackService;
use App\Services\Manage\ComponentAssociationService;
use App\Services\Public\FeaturedModuleService;
use Exception;

class HandleDeleteChallengePathAssociatedData
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeleteChallengePathAssociatedData $event)
    {
        try {
            $challenge_path_id = $event->challengePathId;
            $challengePathAchievement = ChallengePathAchievementsService::deleteChallengePathAchievement($challenge_path_id);
            if (!$challengePathAchievement) {
                return false;
            }

            $challengePathSkillGroupStack = ChallengePathSkillsGroupsStackService::deleteChallengePathSkillGroupStack($challenge_path_id);
            if (!$challengePathSkillGroupStack) {
                return false;
            }
            $componentAssociation = ComponentAssociationService::deleteChallengePathAssociation($challenge_path_id);
            if (!$componentAssociation) {
                return false;
            }
            $featuredModule = FeaturedModuleService::deleteFeaturedModule(config('constants.module_type.challenge_paths'), $challenge_path_id);
            if (!$featuredModule) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
