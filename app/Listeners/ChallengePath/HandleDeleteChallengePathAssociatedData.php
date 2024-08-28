<?php

namespace App\Listeners\ChallengePath;

use App\Events\ChallengePath\DeleteChallengePathAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\Manage\ChallengePathAchievementsService;
use App\Services\Manage\ChallengePathSkillsGroupsStackService;
use App\Services\Manage\ChallengePathTagsGroupsService;
use App\Services\Manage\ComponentAssociationService;
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

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
