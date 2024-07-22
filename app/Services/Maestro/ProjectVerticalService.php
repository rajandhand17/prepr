<?php

namespace App\Services\Maestro;

use App\Models\Language;
use App\Models\ProjectVertical;
use Exception;

class ProjectVerticalService
{
    public static function getProjectVertical()
    {
        try {
            return ProjectVertical::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectVerticalStatus()
    {
        try {
            return ['1' => 'Active', '0' => 'Not Active'];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateProjectVertical($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1)->get();
            if ($moduleMode === 'create') {
                $projectVertical = new ProjectVertical();
            } else {
                $projectVertical = ProjectVertical::find($id);
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
                $projectVertical->$columName = $request->$columName;
            }

            $projectVertical->status = $request->status;
            if ($projectVertical->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findProjectVertical($id)
    {
        try {
            return ProjectVertical::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProjectVertical($projectVertical)
    {
        try {
            return $projectVertical->delete();
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getVerticals()
    {
        try {
            return ProjectVertical::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            return false;
        }
    }
}
