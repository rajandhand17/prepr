<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePath;
use App\Models\ChallengePathTemplate;
use Exception;

class ChallengePathTemplateService
{
    public static function addChallengePathTemplate($challengePathId)
    {
        try {
            $originalChallengePath = ChallengePath::find($challengePathId);
            $challengesPathTemplate = new ChallengePathTemplate();
            $challengesPathTemplate->uuid = $originalChallengePath->uuid;
            $challengesPathTemplate->language = $originalChallengePath->language;
            $challengesPathTemplate->title = $originalChallengePath->title;
            $challengesPathTemplate->slug = $originalChallengePath->slug;
            $challengesPathTemplate->description = $originalChallengePath->description;
            $challengesPathTemplate->user_id = $originalChallengePath->user_id;
            $challengesPathTemplate->organization_id = $originalChallengePath->organization_id;
            $challengesPathTemplate->category_id = $originalChallengePath->category_id;
            $challengesPathTemplate->duration_id = $originalChallengePath->duration_id;
            $challengesPathTemplate->level_id = $originalChallengePath->level_id;
            $challengesPathTemplate->media_type = $originalChallengePath->media_type;
            $challengesPathTemplate->media = $originalChallengePath->media;
            $challengesPathTemplate->privacy = $originalChallengePath->privacy;
            $challengesPathTemplate->status = $originalChallengePath->status;
            $challengesPathTemplate->is_achievement_enabled = $originalChallengePath->is_achievement_enabled;
            $challengesPathTemplate->is_sequential = $originalChallengePath->is_sequential;
            $challengesPathTemplate->is_auto_created = $originalChallengePath->is_auto_created;
            $challengesPathTemplate->save();

            return $challengesPathTemplate;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
