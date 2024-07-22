<?php

namespace App\Services\Maestro;

use App\Models\Language;
use App\Models\ProjectIndustry;
use Exception;

class ProjectIndustryService
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

    public static function getProjectIndustry()
    {
        try {
            return ProjectIndustry::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getProjectIndustryStatus()
    {
        try {
            return ['1' => 'Active', '0' => 'Not Active'];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdateProjectIndustry($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1)->get();
            if ($moduleMode === 'create') {
                $ProjectIndustry = new ProjectIndustry();
            } else {
                $ProjectIndustry = ProjectIndustry::find($id);
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
                $ProjectIndustry->$columName = $request->$columName;
            }

            $ProjectIndustry->status = $request->status;
            if ($ProjectIndustry->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findProjectIndustry($id)
    {
        try {
            return ProjectIndustry::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteProjectIndustry($ProjectIndustry)
    {
        try {
            return $ProjectIndustry->delete();
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getIndustries()
    {
        try {
            return ProjectIndustry::where('status', '1')->pluck('title', 'id')->prepend('Please Select', '');
        } catch (Exception $e) {
            return false;
        }
    }
}
