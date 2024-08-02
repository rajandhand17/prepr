<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ProjectType;
use Exception;

class ProjectTypeService
{
    public static function getProjectType()
    {
        try {
            return ProjectType::query()->latest();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function storeUpdateProjectType($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode === 'create') {
                $projectType = new ProjectType();
            } else {
                $projectType = ProjectType::find($id);
            }

            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $projectType->$columName = $request->$columName;
            }

            $projectType->status = $request->status;
            if ($projectType->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function findProjectType($id)
    {
        try {
            return ProjectType::findOrFail($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteProjectType($projectType)
    {
        try {
            return $projectType->delete();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getTypes()
    {
        try {
            return ProjectType::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
