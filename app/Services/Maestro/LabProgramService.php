<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\LabProgram;
use Exception;

class LabProgramService
{
    public static function getList($getPreSelectedLabTemplates, $language)
    {
        try {
            return LabProgram::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $language)->orderBy('id', 'DESC')->pluck('title', 'id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabProgramList($request)
    {
        try {
            $searched = $request->search;
            $modules = LabProgram::orderBy('id', 'DESC')->where('privacy', '0')->where('language', $request->language);
            if (!empty($searched)) {
                $modules = $modules->where('title', 'like', '%'.$searched.'%');
            }
            $modules = $modules->pluck('title', 'id');

            return $modules;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
