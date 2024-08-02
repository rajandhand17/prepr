<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\PreBuiltAchievement;
use Exception;

class PreBuiltAchievementService
{
    public static function getPreBuiltAchievement()
    {
        try {
            return PreBuiltAchievement::query()->latest();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function storeUpdatePreBuiltAchievement($request, $id, $moduleMode)
    {
        try {
            $achievementImage = self::uploadAchievementImage($request);

            if ($moduleMode === 'create') {
                $achievement = new PreBuiltAchievement();
            } else {
                $achievement = PreBuiltAchievement::find($id);
                $achievementImage = !empty($achievementImage) ? $achievementImage : $achievement->image;
            }

            $languages = LanguageService::getAllActiveLanguages();
            if (!empty($languages)) {
                foreach ($languages as $single) {
                    $columName = UtilityHelper::getColumName($single->iso, 'title');
                    $achievement->$columName = $request->$columName;
                }
            }

            $component_type = self::getComponentType($request);
            $achievement_type = self::getAchievementType($request);
            $achievement->achievement_image = !empty($achievementImage) ? $achievementImage : null;
            $achievement->component_type = !empty($component_type) ? implode(',', $component_type) : null;
            $achievement->achievement_type = $achievement_type;
            $achievement->points = $request->points;
            $achievement->status = $request->status;
            if ($achievement->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function findPreBuiltAchievement($id)
    {
        try {
            return PreBuiltAchievement::findOrFail($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deletePreBuiltAchievement($achievement)
    {
        try {
            return $achievement->delete();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getComponentType($request)
    {
        try {
            $component_type = [];
            if (isset($request->challenge) && $request->challenge == 'on') {
                array_push($component_type, 'challenge');
            }
            if (isset($request->challenge_path) && $request->challenge_path == 'on') {
                array_push($component_type, 'challenge_path');
            }
            if (isset($request->lab) && $request->lab == 'on') {
                array_push($component_type, 'lab');
            }
            if (isset($request->lab_program) && $request->lab_program == 'on') {
                array_push($component_type, 'lab_program');
            }
            if (isset($request->resource_group) && $request->resource_group == 'on') {
                array_push($component_type, 'resource_group');
            }
            if (isset($request->learning_path) && $request->learning_path == 'on') {
                array_push($component_type, 'learning_path');
            }

            return $component_type;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function uploadAchievementImage($request)
    {
        try {
            $achievementImage = null;
            if ($request->file('image')) {
                $achievementImage = FileUploadHelper::uploadImageToS3($request->file('image'), 'pre_built_achievement');
            }

            return $achievementImage;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getAchievementType($request)
    {
        try {
            $achievement_type = '0';
            if (isset($request->challenge) && $request->challenge == 'on') {
                switch ($request->achievement_type) {
                    case 'participation':
                        $achievement_type = '1';
                        break;
                    case 'winner':
                        $achievement_type = '2';
                        break;
                    default:
                        $achievement_type = '0';
                }
            }

            return $achievement_type;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
