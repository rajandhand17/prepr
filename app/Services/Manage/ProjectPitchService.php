<?php

namespace App\Services\Manage;

use App\Models\ProjectPitchValue;
use App\Models\ProjectTaskValue;
use Carbon\Carbon;
use Exception;

class ProjectPitchService
{
    public function createProjectPitchTaskAnswer($projectId, $request)
    {
        try {
            $templateId = $request->template_id;
            if (isset($request->pitch_id)) {
                foreach ($request->pitch_id as $key => $value) {
                    $pitchId = $request['pitch_id'][$key];
                    $pitchAnswer = $request['pitch_answer'][$key];
                    
                    if ($pitchId != null && $pitchAnswer != null) {
                        $createPitch = self::insertPitchData($projectId, $templateId, $pitchId, $pitchAnswer);
                        if (!$createPitch) {
                            return false;
                        }
                    }
                }
            }

            if (isset($request->task_id)) {
                foreach ($request->task_id as $key => $value) {
                    $taskId = $request['task_id'][$key];
                    $taskAnswer = $request['task_answer'][$key];

                    if ($taskId != null && $taskAnswer != null) {
                        $createTask = self::insertTaskData($projectId, $templateId, $taskId, $taskAnswer);
                        if (!$createTask) {
                            return false;
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function insertPitchData($projectId, $templateId, $pitchId, $pitchAnswer)
    {
        try {
            $checkPitchAnswer = ProjectPitchValue::where(['project_id' => $projectId, 'pitch_template_id' => $templateId, 'project_pitch_id' => $pitchId])->first();
            if ($checkPitchAnswer) {
                $pitchData = $checkPitchAnswer;
            } else {
                $pitchData = new ProjectPitchValue();
            }
            
            $pitchData->project_id = $projectId;
            $pitchData->pitch_template_id = $templateId;
            $pitchData->project_pitch_id = $pitchId;
            $pitchData->description = $pitchAnswer;
            $pitchData->save();

            return $pitchData;
        } catch (Exception $e) {
            return false;
        }
    }

    public function insertTaskData($projectId, $templateId, $taskId, $taskAnswer)
    {
        try {

            switch ($taskAnswer) {
                case 'yes':
                    $taskAnswerValue = '1';
                    break;
                case 'no':
                    $taskAnswerValue = '0';
                    break;
                default:
                    $taskAnswerValue = '0';
                    break;
            }

            $checkTaskAnswer = ProjectTaskValue::where(['project_id' => $projectId, 'task_template_id' => $templateId, 'project_task_id' => $taskId])->first();
            if ($checkTaskAnswer) {
                $taskData = $checkTaskAnswer;
            } else {
                $taskData = new ProjectTaskValue();
            }

            $taskData->project_id = $projectId;
            $taskData->task_template_id = $templateId;
            $taskData->project_task_id = $taskId;
            $taskData->status = $taskAnswerValue;
            $taskData->completed_date = Carbon::now()->toDateTimeString();
            $taskData->save();

            return $taskData;
        } catch (Exception $e) {
            return false;
        }
    }
}
