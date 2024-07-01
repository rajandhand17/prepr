<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePath;
use App\Models\ChallengePathTemplate;
use App\Models\Organization;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ChallengePathTemplateService
{
    public function addChallengePathTemplate($challengePathId)
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

    public function redeemChallengePathTemplateToChallengePath($challengePathTemplateId, $organizationId)
    {
        try {
            $challengePathTemplateData = ChallengePathTemplate::find($challengePathTemplateId);
            $organisationName = Organization::where('id', $organizationId)->pluck('title')->first();

            $model = new ChallengePath();
            $slug = UtilityHelper::generateSlug($organisationName.'-'.$challengePathTemplateData->slug, $model);

            $title = $title_format = $organisationName.' '.$challengePathTemplateData->title;
            $next = 1;
            while (ChallengePath::where('title', '=', $title)->first()) {
                $title = "{$title_format} {$next}";
                $next++;
            }

            $challengePath = new ChallengePath();
            $challengePath->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challengePath->language = $challengePathTemplateData->language;
            $challengePath->slug = $slug;
            $challengePath->title = $challengePathTemplateData->title;
            $challengePath->description = $challengePathTemplateData->description;
            $challengePath->user_id = auth()->user()->id;
            $challengePath->organization_id = $organizationId;
            $challengePath->category_id = $challengePathTemplateData->category_id;
            $challengePath->duration_id = $challengePathTemplateData->duration_id;
            $challengePath->level_id = $challengePathTemplateData->level_id;
            $challengePath->media_type = $challengePathTemplateData->media_type;
            $challengePath->media = $challengePathTemplateData->getRawOriginal('media');
            $challengePath->privacy = $challengePathTemplateData->privacy;
            $challengePath->status = $challengePathTemplateData->status;
            $challengePath->is_achievement_enabled = $challengePathTemplateData->is_achievement_enabled;
            $challengePath->is_sequential = $challengePathTemplateData->is_sequential;
            $challengePath->is_auto_created = $challengePathTemplateData->is_auto_created;
            $challengePath->save();

            return $challengePath;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengePathBasedOnId($id)
    {
        try {
            return ChallengePathTemplate::where('id', $id)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
