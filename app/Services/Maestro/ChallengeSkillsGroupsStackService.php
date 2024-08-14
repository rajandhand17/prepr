<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeSkillsGroupsStack;
use Exception;

class ChallengeSkillsGroupsStackService
{
    public static function challengeSkillsGroupsStacks($request, $challenge)
    {
        try {
            if (!empty($request->skills)) {
                if (ChallengeSkillsGroupsStack::where(['challenge_id' => $challenge, 'type' => '0'])->exists()) {
                    ChallengeSkillsGroupsStack::where(['challenge_id' => $challenge, 'type' => '0'])->delete();
                }
                $skillNewArray = [];
                foreach ($request->skills as $skill) {
                    $skillData['challenge_id'] = $challenge->id;
                    $skillData['foreign_id'] = $skill;
                    $skillData['type'] = '0';
                    $skillNewArray[] = $skillData;
                }
                ChallengeSkillsGroupsStack::insert($skillNewArray);
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPluckSkillGroupStack($challenge)
    {
        try {
            return ChallengeSkillsGroupsStack::where(['challenge_id' => $challenge->id, 'type' => '0'])->pluck('foreign_id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
