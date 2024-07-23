<?php

namespace App\Services\Maestro;

use App\Models\Language;
use App\Models\ProjectType;
use Exception;

class ProjectTypeService
{
    public static function getProjectType()
    {
        try {
            return ProjectType::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectTypeStatus()
    {
        try {
            return ['1' => 'Active', '0' => 'Not Active'];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateProjectType($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1)->get();
            if ($moduleMode === 'create') {
                $projectType = new ProjectType();
            } else {
                $projectType = ProjectType::find($id);
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
                $projectType->$columName = $request->$columName;
            }

            $projectType->status = $request->status;
            if ($projectType->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findProjectType($id)
    {
        try {
            return ProjectType::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProjectType($projectType)
    {
        try {
            return $projectType->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getTypes()
    {
        try {
            return ProjectType::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            return false;
        }
    }
}
