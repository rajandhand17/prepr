<?php

namespace App\Services\Manage;

use App\Models\ChallengeSponsor;
use App\Models\ChallengeTemplateSponsor;

class ChallengeTemplateSponsorService
{
    public function createChallengeTemplateSponsor($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeSponsor = ChallengeSponsor::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeSponsor as $getSponsor) {
                $createChallengeSponsor = new ChallengeTemplateSponsor();
                $createChallengeSponsor->template_challenge_id = $templateChallengeId;
                $createChallengeSponsor->host_id = $getSponsor->host_id;
                $createChallengeSponsor->save();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
