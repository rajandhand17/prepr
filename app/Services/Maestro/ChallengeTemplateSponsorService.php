<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeSponsor;
use App\Models\ChallengeTemplateSponsor;
use Exception;

class ChallengeTemplateSponsorService
{
    public static function addChallengeTemplateSponsor($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeSponsor = ChallengeSponsor::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeSponsor as $getSponsor) {
                $createChallengeSponsor = new ChallengeTemplateSponsor();
                $createChallengeSponsor->challenge_template_id = $templateChallengeId;
                $createChallengeSponsor->host_id = $getSponsor->host_id;
                $createChallengeSponsor->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
