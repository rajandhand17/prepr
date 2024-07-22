<?php

namespace App\Services\Maestro;

use App\Models\Language;
use App\Models\ProjectStatus;
use Exception;

class ProjectStatusService
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

    public static function getProjectStatus()
    {
        try {
            return ProjectStatus::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectStatusStatus()
    {
        try {
            return ['1' => 'Active', '0' => 'Not Active'];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateProjectStatus($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1)->get();
            if ($moduleMode === 'create') {
                $projectStatus = new ProjectStatus();
            } else {
                $projectStatus = ProjectStatus::find($id);
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
                $projectStatus->$columName = $request->$columName;
            }

            $projectStatus->status = $request->status;
            if ($projectStatus->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findProjectStatus($id)
    {
        try {
            return ProjectStatus::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProjectStatus($projectStatus)
    {
        try {
            return $projectStatus->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getStatus()
    {
        try {
            return ProjectStatus::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            return false;
        }
    }
}
