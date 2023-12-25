<?php

namespace App\Services\Manage;

use App\Models\ChallengeExternalLink;
use App\Models\TemplateChallengeExternalLink;
use Exception;

class ChallengeTemplateExternalLinkService
{
    public function createChallengeTemplateExternalLink($challengeId, $templateChallengeId)
    {
        try {
            $challengeExternalLinks = ChallengeExternalLink::where('challenge_id', $challengeId)->get();
            if ($challengeExternalLinks) {
                foreach ($challengeExternalLinks as $challengeExternalLink) {
                    $templateChallengeExternalLink = new TemplateChallengeExternalLink();
                    $templateChallengeExternalLink->template_challenge_id = $templateChallengeId;
                    $templateChallengeExternalLink->custom_timelines_title = $challengeExternalLink->custom_timelines_title;
                    $templateChallengeExternalLink->custom_timelines_description = $challengeExternalLink->custom_timelines_description;
                    $templateChallengeExternalLink->custom_timelines_duration = $challengeExternalLink->custom_timelines_duration;
                    $templateChallengeExternalLink->schedule_custom_notify = $challengeExternalLink->schedule_custom_notify;
                    $templateChallengeExternalLink->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
