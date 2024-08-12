<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabProgram;
use Exception;

class ChallengePathService
{
    public static function getList($getPreSelectedLabTemplates,$language)
    {
        try {
            return ChallengePathService::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $language)->orderBy('id', 'DESC')->pluck('title', 'id');
        }catch (Exception $e){
            UtilityHelper::logError($e);
            return false;
        }
    }
}
