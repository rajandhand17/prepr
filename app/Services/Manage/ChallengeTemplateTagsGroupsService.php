<?php

namespace App\Services\Manage;

use App\Models\ChallengeTagsGroups;
use App\Models\ChallengeTemplateTagsGroups;

class ChallengeTemplateTagsGroupsService
{
    public function createChallengeTemplateTagsGroups($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeTags = ChallengeTagsGroups::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeTags as $key => $value) {
                $templateChallengeTags = new ChallengeTemplateTagsGroups();
                $templateChallengeTags->template_challenge_id = $templateChallengeId;
                $templateChallengeTags->foreign_id = $value->foreign_id;
                $templateChallengeTags->type = $value->type;
                $templateChallengeTags->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
