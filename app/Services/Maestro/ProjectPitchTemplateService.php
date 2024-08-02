<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePitch;
use App\Models\ChallengeTask;
use App\Models\Language;
use App\Models\PitchTemplate;
use Exception;

class ProjectPitchTemplateService
{
    public static function findPitchTemplate($id)
    {
        try {
            return PitchTemplate::findOrFail($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPitchTemplate()
    {
        try {
            return PitchTemplate::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function storeUpdatePitchTemplate($request, $id, $moduleMode)
    {
        try {
            $languages = LanguageService::getAllActiveLanguages();
            if ($moduleMode == 'edit') {
                $pitchTemplate = PitchTemplate::findOrFail($id);
            } else {
                $pitchTemplate = new PitchTemplate();
            }
            foreach ($languages as $key => $single) {
                $columName = UtilityHelper::getColumName($single->iso, 'title');
                $pitchTemplate->$columName = $request->$columName;
            }
            if ($pitchTemplate->save()) {
                return $pitchTemplate;
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPitchSectionById($id)
    {
        try {
            return ChallengePitch::where('template_id', $id)->get();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getPitchTaskById($id)
    {
        try {
            return ChallengeTask::where('template_id', $id)->get();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function storePitchTemplate($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1);
            $pitchTemplate = new PitchTemplate();
            foreach ($languages->get() as $key => $single) {
                if ($single->iso == 'en') {
                    $columName = 'title';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName = $columName.'_title';
                }
                $pitchTemplate->$columName = $request->$columName;
            }
            if ($pitchTemplate->save()) {
                $pitchSectionArray = [];
                $pitchData = array_map(null, $request->pitch['name']['en'], $request->pitch['description']['fr-CA']);
                foreach ($pitchData as $pitch) {
                    $pitchSection['title'] = $pitch[0];
                    $pitchSection['description'] = $pitch[1];
                    $pitchSection['fr_CA_title'] = $pitch[0];
                    $pitchSection['fr_CA_description'] = $pitch[1];
                    $pitchSection['template_id'] = $pitchTemplate->id;
                    $pitchSectionArray[] = $pitchSection;
                }
                ChallengePitch::insert($pitchSectionArray);

                $pitchTasksData = array_map(null, $request->pitch['task']['en'], $request->pitch['task']['fr-CA']);
                $pitchTasksArray = [];
                foreach ($pitchTasksData as $task) {
                    $pitchTasks['title'] = $task[0];
                    $pitchTasks['fr_CA_title'] = $task[0];
                    $pitchTasks['template_id'] = $pitchTemplate->id;
                    $pitchTasksArray[] = $pitchTasks;
                }
                ChallengeTask::insert($pitchTasksArray);

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updatePitchTemplate($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1)->get();
            $pitchTemplate = PitchTemplate::findOrFail($id);
            foreach ($languages as $key => $single) {
                if ($single->iso == 'en') {
                    $columName = 'title';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName = $columName.'_title';
                }
                $pitchTemplate->$columName = $request->$columName;
            }
            if ($pitchTemplate->save()) {
                $pitchSectionArray = [];
                $pitchData = array_map(null, $request->pitch['name']['en'], $request->pitch['description']['fr-CA']);
                foreach ($pitchData as $pitch) {
                    $pitchSection['title'] = $pitch[0];
                    $pitchSection['description'] = $pitch[1];
                    $pitchSection['fr_CA_title'] = $pitch[0];
                    $pitchSection['fr_CA_description'] = $pitch[1];
                    $pitchSection['template_id'] = $pitchTemplate->id;
                    $pitchSectionArray[] = $pitchSection;
                }
                ChallengePitch::insert($pitchSectionArray);

                $pitchTasksData = array_map(null, $request->pitch['task']['en'], $request->pitch['task']['fr-CA']);
                $pitchTasksArray = [];
                foreach ($pitchTasksData as $task) {
                    $pitchTasks['title'] = $task[0];
                    $pitchTasks['fr_CA_title'] = $task[0];
                    $pitchTasks['template_id'] = $pitchTemplate->id;
                    $pitchTasksArray[] = $pitchTasks;
                }
                ChallengeTask::insert($pitchTasksArray);

                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deletePitchTemplate($pitchTemplate)
    {
        try {
            return $pitchTemplate->delete();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
