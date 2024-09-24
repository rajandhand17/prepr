<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengeTask;
use Exception;

class ChallengeTaskService
{
    public static function getChallengeTaskById($id)
    {
        try {
            return ChallengeTask::where('template_id', $id)->get();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function saveChallengeTask($request, $pitchTemplate)
    {
        try {
            $pitchTasksData = array_map(null, $request->pitch['task']['en'], $request->pitch['task']['fr-CA']);
            $pitchTasksArray = [];
            if(!empty($pitchTasksData)){
                foreach ($pitchTasksData as $task) {
                    $pitchTasks['title'] = $task[0];
                    $pitchTasks['fr_CA_title'] = $task[1];
                    $pitchTasks['template_id'] = $pitchTemplate->id;
                    $pitchTasksArray[] = $pitchTasks;
                }
                if(!empty($pitchTasksArray)){
                    ChallengeTask::where('template_id',$pitchTemplate->id)->delete();
                    ChallengeTask::insert($pitchTasksArray);
                }
            }
            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
