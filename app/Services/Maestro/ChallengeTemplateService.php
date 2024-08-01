<?php

namespace App\Services\Maestro;

use App\Events\ChallengeTemplate\DeleteChallengeTemplateAssociatedData;
use App\Helpers\UtilityHelper;
use App\Models\ChallengeTemplate;
use Exception;

class ChallengeTemplateService
{
    public static function getChallengesTemplate()
    {
        try {
            return ChallengeTemplate::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getChallengeTemplateBasedOnId($id)
    {
        try {
            return ChallengeTemplate::where('id', $id)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengeTemplate($slug, $challengeTemplateId)
    {
        try {
            $challengeTemplate = ChallengeTemplate::where('slug', $slug)->delete();
            if ($challengeTemplate) {
                event(new DeleteChallengeTemplateAssociatedData($challengeTemplateId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
