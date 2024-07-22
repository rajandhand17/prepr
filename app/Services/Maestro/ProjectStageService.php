<?php

namespace App\Services\Maestro;

use App\Models\Language;
use App\Models\ProjectStage;
use Exception;

class ProjectStageService
{
    public static function getLanguage()
    {
        try {
            $language = Language::where('status', 1)->get();
            if ($language != null) {
                return $language;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectStage()
    {
        try {
            return ProjectStage::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectStageStatus()
    {
        try {
            return ['1' => 'Active', '0' => 'Not Active'];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateProjectStage($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1)->get();
            if ($moduleMode === 'create') {
                $projectStage = new ProjectStage();
            } else {
                $projectStage = ProjectStage::find($id);
            }

            foreach ($languages as $single) {
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
