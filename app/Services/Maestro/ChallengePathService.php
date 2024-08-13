<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePath;
use App\Models\ChallengeTemplate;
use App\Models\LabProgram;
use Exception;

class ChallengePathService
{
    public static function getList($getPreSelectedLabTemplates,$language)
    {
        try {
            return ChallengePath::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $language)->orderBy('id', 'DESC')->pluck('title', 'id');
        }catch (Exception $e){
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getChallengePathList($request){
        try {
            $searched = $request->search;
            $modules = ChallengePath::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language);
            if (!empty($searched)) {
                $modules = $modules->where('title', 'like', '%'.$searched.'%');
            }
            $modules = $modules->pluck('title', 'id');
            return $modules;
        }catch (Exception $e){
            UtilityHelper::logError($e);
            return false;
        }
    }
}
