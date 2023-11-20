<?php

namespace App\Services\Manage;

use App\Helpers\LanguageColumnHelper;
use App\Models\ChallengePitch;
use App\Models\ChallengeTask;
use App\Models\ProjectPitchValue;
use App\Models\ProjectTaskValue;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Schema;

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
                    $completedAt = Carbon::now()->toDateTimeString();
                    break;
                case 'no':
                    $taskAnswerValue = '0';
                    $completedAt = null;
                    break;
                default:
                    $taskAnswerValue = '0';
                    $completedAt = null;
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
            $taskData->completed_date = $completedAt;
            $taskData->save();

            return $taskData;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getPitchAnswerBasedOnId($pitchData, $projectId, $projectLanguage)
    {
        try {
            if ($projectLanguage == 'en') {
                $challenge_pitch = ChallengePitch::select('id', 'title', 'description')->where('id', $pitchData->id)->first();
            } else {
                //get column name based on language
                $column_name_title = LanguageColumnHelper::getLanguageColumnName($projectLanguage, 'title');
                $column_name_description = LanguageColumnHelper::getLanguageColumnName($projectLanguage, 'description');

                //check whether the column exist in the db or not
                if (!$column_name_title || !Schema::hasColumn('challenge_pitches', $column_name_title)) {
                    return false;
                }

                if (!$column_name_description || !Schema::hasColumn('challenge_pitches', $column_name_description)) {
                    return false;
                }
                $challenge_pitch = ChallengePitch::select('id', $column_name_title.' as title', $column_name_description.' as description')->where('id', $pitchData->id)->first();
            }

            $checkPitchAnswer = ProjectPitchValue::where(['project_id' => $projectId, 'pitch_template_id' => $pitchData->template_id, 'project_pitch_id' => $pitchData->id])->first();
            $challenge_pitch->descriptionAnswer = null;
            if ($checkPitchAnswer) {
                $challenge_pitch->descriptionAnswer = ($checkPitchAnswer->description != null) ? $checkPitchAnswer->description : null;
            }

            return $challenge_pitch;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getTaskAnswerBasedOnId($taskData, $projectId, $projectLanguage)
    {
        try {
            if ($projectLanguage == 'en') {
                $challenge_task = ChallengeTask::select('id', 'title')->where('id', $taskData->id)->first();
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($projectLanguage, 'title');

                //check whether the column exist in the db or not

                if (!$column_name || !Schema::hasColumn('challenge_tasks', $column_name)) {
                    return false;
                }
                $challenge_task = ChallengeTask::select('id', $column_name.' as title')->where('id', $taskData->id)->first();
            }

            $checkTaskAnswer = ProjectTaskValue::where(['project_id' => $projectId, 'task_template_id' => $taskData->template_id, 'project_task_id' => $taskData->id])->first();
            $challenge_task->isCompleted = 'no';
            $challenge_task->completedAt = null;
            if ($checkTaskAnswer) {
                $challenge_task->isCompleted = ($checkTaskAnswer->status == '1') ? 'yes' : 'no';
                $challenge_task->completedAt = ($checkTaskAnswer->completed_date != null) ? $checkTaskAnswer->completed_date : null;
            }

            return $challenge_task;
        } catch (Exception $e) {
            return false;
        }
    }
}
