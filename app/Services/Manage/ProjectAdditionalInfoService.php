<?php

namespace App\Services\Manage;

use App\Models\ProjectAdditionalInfo;
use Exception;

class ProjectAdditionalInfoService
{
    public function addUpdateAdditionalInfoService($request, $projectId)
    {
        try {
            $checkProjectAdditionalInfo = ProjectAdditionalInfo::where('project_id', $projectId)->first();
            if ($checkProjectAdditionalInfo) {
                $newprojectAdditionalInfo = $checkProjectAdditionalInfo;
            } else {
                $newprojectAdditionalInfo = new ProjectAdditionalInfo();
            }

            $newprojectAdditionalInfo->project_id = $projectId;
            $newprojectAdditionalInfo->category_id = $request->category_id ?? null;
            $newprojectAdditionalInfo->industry_id = $request->industry_id ?? null;
            $newprojectAdditionalInfo->verticals_id = $request->verticals_id ?? null;
            $newprojectAdditionalInfo->type_id = $request->type_id ?? null;
            $newprojectAdditionalInfo->stage_id = $request->stage_id ?? null;
            $newprojectAdditionalInfo->status_id = $request->status_id ?? null;
            $newprojectAdditionalInfo->save();

            return $newprojectAdditionalInfo;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProjectAdditionalInfo($projectId)
    {
        try {
            $checkProjectAdditionalInfo = ProjectAdditionalInfo::where('project_id', $projectId)->first();
            if ($checkProjectAdditionalInfo) {
                $checkProjectAdditionalInfo->delete();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
