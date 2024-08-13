<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeExternalLink;
use App\Models\ChallengeTemplateExternalLink;
use Exception;

class ChallengeTemplateExternalLinkService
{
    public static function addChallengeTemplateExternalLink($challengeId, $templateChallengeId)
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
}
