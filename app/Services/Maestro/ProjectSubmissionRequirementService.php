<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ProjectSubmissionRequirement;
use Exception;

class ProjectSubmissionRequirementService
{
    public static function getSubmissionRequirement()
    {
        try {
            return ProjectSubmissionRequirement::query()->latest();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function storeUpdateSubmissionRequirement($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode === 'create') {
                $submissionRequirement = new ProjectSubmissionRequirement();
            } else {
                $submissionRequirement = ProjectSubmissionRequirement::find($id);
            }
            if (!empty($languages)) {
                foreach ($languages as $single) {
                    $columName = UtilityHelper::getColumName($single->iso, 'title');
                    $submissionRequirement->$columName = $request->$columName;
                }
            }
            $submissionRequirement->status = $request->status;
            if ($submissionRequirement->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function findSubmissionRequirement($id)
    {
        try {
            return ProjectSubmissionRequirement::findOrFail($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteSubmissionRequirement($submissionRequirement)
    {
        try {
            return $submissionRequirement->delete();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
