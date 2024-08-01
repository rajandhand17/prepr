<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ProjectVertical;
use Exception;

class ProjectVerticalService
{
    public static function getProjectVertical()
    {
        try {
            return ProjectVertical::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateProjectVertical($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode === 'create') {
                $projectVertical = new ProjectVertical();
            } else {
                $projectVertical = ProjectVertical::find($id);
            }

            foreach ($languages as $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $projectVertical->$columName = $request->$columName;
            }

            $projectVertical->status = $request->status;
            if ($projectVertical->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findProjectVertical($id)
    {
        try {
            return ProjectVertical::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProjectVertical($projectVertical)
    {
        try {
            return $projectVertical->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getVerticals()
    {
        try {
            return ProjectVertical::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            return false;
        }
    }
}
