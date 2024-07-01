<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeSponsor;
use App\Models\ChallengeTemplateSponsor;
use Exception;

class ChallengeTemplateSponsorService
{
    public function addChallengeTemplateSponsor($challengeId, $templateChallengeId)
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

    public function redeemChallengeTemplateSponsor($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateSponsors = ChallengeTemplateSponsor::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateSponsors)) {
                foreach ($checkChallengeTemplateSponsors as $challengeTemplateSponsor) {
                    $newChallengeSponsors = new ChallengeSponsor();
                    $newChallengeSponsors->challenge_id = $redeemChallengeId;
                    $newChallengeSponsors->host_id = $challengeTemplateSponsor->host_id;
                    $newChallengeSponsors->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengeTemplateSponsor($challengeTemplateId)
    {
        try {
            $challengeTemplateSponsor = ChallengeTemplateSponsor::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateSponsor->isNotEmpty()) {
                $deleteChallengeTemplateSponsor = ChallengeTemplateSponsor::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateSponsor) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
