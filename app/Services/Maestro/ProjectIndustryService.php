<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ProjectIndustry;
use Exception;

class ProjectIndustryService
{
    public static function getProjectIndustry()
    {
        try {
            return ProjectIndustry::query()->latest();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function storeUpdateProjectIndustry($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode === 'create') {
                $ProjectIndustry = new ProjectIndustry();
            } else {
                $ProjectIndustry = ProjectIndustry::find($id);
            }

            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $ProjectIndustry->$columName = $request->$columName;
            }

            $ProjectIndustry->status = $request->status;
            if ($ProjectIndustry->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function findProjectIndustry($id)
    {
        try {
            return ProjectIndustry::findOrFail($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteProjectIndustry($ProjectIndustry)
    {
        try {
            return $ProjectIndustry->delete();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getIndustries()
    {
        try {
            return ProjectIndustry::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
