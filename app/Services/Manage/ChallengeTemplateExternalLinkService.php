<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeExternalLink;
use App\Models\ChallengeTemplateExternalLink;
use Exception;

class ChallengeTemplateExternalLinkService
{
    public function addChallengeTemplateExternalLink($challengeId, $templateChallengeId)
    {
        try {
            $challengeExternalLinks = ChallengeExternalLink::where('challenge_id', $challengeId)->get();
            if ($challengeExternalLinks) {
                foreach ($challengeExternalLinks as $challengeExternalLink) {
                    $challengeTemplateExternalLink = new ChallengeTemplateExternalLink();
                    $challengeTemplateExternalLink->challenge_template_id = $templateChallengeId;
                    $challengeTemplateExternalLink->social_media_link = $challengeExternalLink->social_media_link;
                    $challengeTemplateExternalLink->social_link_id = $challengeExternalLink->social_link_id;
                    $challengeTemplateExternalLink->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function redeemChallengeTemplateExternalLink($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateExternalLinks = ChallengeTemplateExternalLink::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateExternalLinks)) {
                foreach ($checkChallengeTemplateExternalLinks as $challengeExternalLink) {
                    $newChallengeExternalLink = new ChallengeExternalLink();
                    $newChallengeExternalLink->challenge_id = $redeemChallengeId;
                    $newChallengeExternalLink->social_media_link = $challengeExternalLink->social_media_link;
                    $newChallengeExternalLink->social_link_id = $challengeExternalLink->social_link_id;
                    $newChallengeExternalLink->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengeTemplateExternalLink($challengeTemplateId)
    {
        try {
            $challengeTemplateExternalLink = ChallengeTemplateExternalLink::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateExternalLink->isNotEmpty()) {
                $deleteChallengeTemplateExternalLink = ChallengeTemplateExternalLink::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateExternalLink) {
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
