<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTagsGroups;
use App\Models\ChallengeTemplateTagsGroups;
use Exception;

class ChallengeTemplateTagsGroupsService
{
    public function addChallengeTemplateTagsGroups($challengeId, $templateChallengeId)
    {
        try {
            $getChallengeTags = ChallengeTagsGroups::where('challenge_id', $challengeId)->get();
            foreach ($getChallengeTags as $key => $value) {
                $templateChallengeTags = new ChallengeTemplateTagsGroups();
                $templateChallengeTags->challenge_template_id = $templateChallengeId;
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

    public function redeemChallengeTemplateTagGroup($redeemChallengeId, $challengeTemplateId)
    {
        try {
            $checkChallengeTemplateTagGroups = ChallengeTemplateTagsGroups::where('challenge_template_id', $challengeTemplateId)->get();
            if (!empty($checkChallengeTemplateTagGroups)) {
                foreach ($checkChallengeTemplateTagGroups as $challengeTemplateTagGroup) {
                    $newChallengeTagGroups = new ChallengeTagsGroups();
                    $newChallengeTagGroups->challenge_id = $redeemChallengeId;
                    $newChallengeTagGroups->foreign_id = $challengeTemplateTagGroup->foreign_id;
                    $newChallengeTagGroups->type = $challengeTemplateTagGroup->type;
                    $newChallengeTagGroups->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengeTemplateTagsGroups($challengeTemplateId)
    {
        try {
            $challengeTemplateTagsGroups = ChallengeTemplateTagsGroups::where('challenge_template_id', $challengeTemplateId)->get();
            if ($challengeTemplateTagsGroups->isNotEmpty()) {
                $deleteChallengeTemplateTagsGroups = ChallengeTemplateTagsGroups::where('challenge_template_id', $challengeTemplateId)->delete();
                if (!$deleteChallengeTemplateTagsGroups) {
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
