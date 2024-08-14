<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePathTagGroup;
use App\Models\ChallengePathTemplateTagsGroups;
use Exception;

class ChallengePathTemplateTagsGroupsService
{
    public static function addChallengePathTemplateTagsGroupsService($challengePathId, $templateChallengePathId)
    {
        try {
            $getChallengeTags = ChallengePathTagGroup::where('challenge_path_id', $challengePathId)->get();
            foreach ($getChallengeTags as $key => $value) {
                $templateChallengeTags = new ChallengePathTemplateTagsGroups();
                $templateChallengeTags->challenge_path_template_id = $templateChallengePathId;
                $templateChallengeTags->foreign_id = $value->foreign_id;
                $templateChallengeTags->type = $value->type;
                $templateChallengeTags->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
