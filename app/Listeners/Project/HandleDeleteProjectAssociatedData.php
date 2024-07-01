<?php

namespace App\Listeners\Project;

use App\Events\Project\DeleteProjectAssociatedData;
use App\Helpers\UtilityHelper;
use App\Services\ProjectAdditionalInfoService;
use App\Services\ProjectFileService;
use App\Services\ProjectPitchService;
use Exception;

class HandleDeleteProjectAssociatedData
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeleteProjectAssociatedData $event)
    {
        try {
            $project_id = $event->projectId;

            $projectPitch = ProjectPitchService::deleteProjectPitch($project_id);
            if (!$projectPitch) {
                return false;
            }

            $projectTask = ProjectPitchService::deleteProjectTask($project_id);
            if (!$projectTask) {
                return false;
            }

            $projectFile = ProjectFileService::deleteProjectFile($project_id);
            if (!$projectFile) {
                return false;
            }

            $projectAdditionalInfo = ProjectAdditionalInfoService::deleteProjectAdditionalInfo($project_id);
            if (!$projectAdditionalInfo) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
