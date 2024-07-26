<?php

namespace App\Services\Maestro;

use App\Helpers\Maestro\UtilityHelper;
use App\Models\ProjectStage;
use Exception;

class ProjectStageService
{
    public static function getProjectStage()
    {
        try {
            return ProjectStage::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateProjectStage($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode === 'create') {
                $projectStage = new ProjectStage();
            } else {
                $projectStage = ProjectStage::find($id);
            }

            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $projectStage->$columName = $request->$columName;
            }

            $projectStage->status = $request->status;
            if ($projectStage->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findProjectStage($id)
    {
        try {
            return ProjectStage::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProjectStage($projectStage)
    {
        try {
            return $projectStage->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectStages()
    {
        try {
            return ProjectStage::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            return false;
        }
    }
}
