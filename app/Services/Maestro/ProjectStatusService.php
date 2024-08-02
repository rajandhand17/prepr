<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ProjectStatus;
use Exception;

class ProjectStatusService
{
    public static function getProjectStatus()
    {
        try {
            return ProjectStatus::query()->latest();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function storeUpdateProjectStatus($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode === 'create') {
                $projectStatus = new ProjectStatus();
            } else {
                $projectStatus = ProjectStatus::find($id);
            }

            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $projectStatus->$columName = $request->$columName;
            }

            $projectStatus->status = $request->status;
            if ($projectStatus->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function findProjectStatus($id)
    {
        try {
            return ProjectStatus::findOrFail($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteProjectStatus($projectStatus)
    {
        try {
            return $projectStatus->delete();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getStatus()
    {
        try {
            return ProjectStatus::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
