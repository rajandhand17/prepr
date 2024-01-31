<?php

namespace App\Services\Manage;

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
                    $challengeTemplateExternalLink->custom_timelines_title = $challengeExternalLink->custom_timelines_title;
                    $challengeTemplateExternalLink->custom_timelines_description = $challengeExternalLink->custom_timelines_description;
                    $challengeTemplateExternalLink->custom_timelines_duration = $challengeExternalLink->custom_timelines_duration;
                    $challengeTemplateExternalLink->schedule_custom_notify = $challengeExternalLink->schedule_custom_notify;
                    $challengeTemplateExternalLink->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
