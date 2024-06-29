<?php

namespace App\Services;

use App\Helpers\LanguageColumnHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengePitch;
use App\Models\ChallengeTask;
use App\Models\PitchTemplate;
use App\Models\ProjectPitchValue;
use App\Models\ProjectTaskValue;
use App\Models\ProjectTemplate;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProjectPitchService
{
    public function addProjectPitchTaskAnswer($projectId, $request)
    {
        try {
            $templateId = $request->template_id;
            $checkProjectTemplate = ProjectTemplate::where(['project_id' => $projectId, 'template_id' => $templateId])->first();
            if ($checkProjectTemplate) {
                $projectTemplate = $checkProjectTemplate;
            } else {
                $projectTemplate = new ProjectTemplate();
            }
            $projectTemplate->project_id = $projectId;
            $projectTemplate->template_id = $templateId;
            $projectTemplate->save();

            if (isset($request->pitch_id)) {
                foreach ($request->pitch_id as $key => $value) {
                    $pitchId = $request['pitch_id'][$key];
                    $pitchAnswer = $request['pitch_answer'][$key];

                    if ($pitchId != null) {
                        $createPitch = self::insertPitchData($projectId, $templateId, $pitchId, $pitchAnswer);
                        if ($createPitch) {
                            $getPitch = ChallengePitch::where('id', $pitchId)->first();
                            $activity = auth()->user()->full_name.' '.__('responses.project_pitch_activty').' '.$getPitch->title;
                            ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);
                        }

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
                        if ($createTask) {
                            $getTask = ChallengeTask::where('id', $taskId)->first();
                            $activity = auth()->user()->full_name.' '.__('responses.project_task_activty').' '.$getTask->title;
                            ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);
                        }

                        if (!$createTask) {
                            return false;
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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
            UtilityHelper::logError($e);
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
            UtilityHelper::logError($e);
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
            $challenge_pitch->description_answer = null;
            if ($checkPitchAnswer) {
                $challenge_pitch->description_answer = ($checkPitchAnswer->description != null) ? $checkPitchAnswer->description : null;
            }

            return $challenge_pitch;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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
            $challenge_task->is_completed = 'no';
            $challenge_task->completed_at = null;
            if ($checkTaskAnswer) {
                $challenge_task->is_completed = ($checkTaskAnswer->status == '1') ? 'yes' : 'no';
                $challenge_task->completed_at = ($checkTaskAnswer->completed_date != null) ? $checkTaskAnswer->completed_date : null;
            }

            return $challenge_task;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function checkProjectPitch($projectId, $templateId)
    {
        try {
            $challengePitchIds = ChallengePitch::where('template_id', $templateId)->pluck('id')->all();
            if (empty($challengePitchIds)) {
                return true; // No challenge pitches, consider complete
            }
            $projectPitchCount = ProjectPitchValue::where(['project_id' => $projectId, 'pitch_template_id' => $templateId])->whereIn('project_pitch_id', $challengePitchIds)->whereNotNull('description')->count();
            $challengePitchCount = count($challengePitchIds);

            return $projectPitchCount === $challengePitchCount;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function checkProjectTask($projectId, $templateId)
    {
        try {
            $challengeTaskIds = ChallengeTask::where('template_id', $templateId)->pluck('id')->all();
            if (empty($challengeTaskIds)) {
                return true; // No challenge task, consider complete
            }
            $projectTaskCount = ProjectTaskValue::where(['project_id' => $projectId, 'task_template_id' => $templateId, 'status' => '1'])->whereIn('project_task_id', $challengeTaskIds)->count();
            $challengeTaskCount = count($challengeTaskIds);

            return $projectTaskCount === $challengeTaskCount;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteProjectPitch($projectId)
    {
        try {
            $checkProjectPitchExists = ProjectPitchValue::where('project_id', $projectId)->pluck('id');
            if ($checkProjectPitchExists->isNotEmpty()) {
                $deleteProjectPitch = ProjectPitchValue::whereIn('id', $checkProjectPitchExists)->delete();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteProjectTask($projectId)
    {
        try {
            $checkProjectTaskExists = ProjectTaskValue::where('project_id', $projectId)->pluck('id');
            if ($checkProjectTaskExists->isNotEmpty()) {
                $deleteProjectTask = ProjectTaskValue::whereIn('id', $checkProjectTaskExists)->delete();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function createChallengeAIProjectPitch($request)
    {
        try {
            $pitchTemplate = PitchTemplate::create([
                'title' => $request['challengeTitle'],
            ]);

            foreach ($request['reflections'] as $reflection) {
                $challengePitchData = [
                    'template_id'       => $pitchTemplate->id,
                    'title'             => $request['language'] === 'en' ? $reflection : '',
                    'description'       => $request['language'] === 'en' ? 'Write your answer here...' : '',
                    'fr_CA_title'       => $request['language'] === 'fr_CA' ? $reflection : '',
                    'fr_CA_description' => $request['language'] === 'fr_CA' ? 'Écrivez la réponse ici...' : '',
                ];

                ChallengePitch::create($challengePitchData);
            }

            return $pitchTemplate;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            Log::error('Error in createChallengeAIProjectPitch in ProjectPitchService.php: '.$e->getMessage());

            return false;
        }
    }
}
