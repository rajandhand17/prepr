<?php

namespace App\Services\Maestro\PreBuiltAchievement;

use App\Models\Language;
use App\Models\PreBuiltAchievement;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class PreBuiltAchievementService
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

    public static function getPreBuiltAchievement()
    {
        try {
            return PreBuiltAchievement::query()->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function storeUpdatePreBuiltAchievement($request, $id, $moduleMode)
    {
        try {
            $languages = Language::where('status', 1)->get();

            if ($request->file('image')) {
                $filename = Str::random(25).'.'.$request->file('image')->getClientOriginalExtension();
                $image = Image::make($request->file('image'))->resize(735, 415)->stream();
                $img = Storage::disk('s3')->put('uploads/ranks/'.$filename, $image);
                $coverImage = 'uploads/ranks/'.$filename;
            } else {
                $coverImage = null;
            }
            if ($moduleMode === 'create') {
                $achievement = new PreBuiltAchievement();
            } else {
                $achievement = PreBuiltAchievement::find($id);
                $coverImage = !empty($coverImage) ? $coverImage : $achievement->image;
            }
            if (!empty($languages)) {
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
                    $achievement->$columName = $request->$columName;
                }
            }

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

            $achievement->achievement_image = $coverImage;
            $achievement->points = $request->points;
            $achievement->component_type = !empty($component_type) ? implode(',', $component_type) : null;
            $achievement->achievement_type = $achievement_type;
            $achievement->status = $request->status;
            if ($achievement->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function findPreBuiltAchievement($id)
    {
        try {
            return PreBuiltAchievement::findOrFail($id);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deletePreBuiltAchievement($achievement)
    {
        try {
            return $achievement->delete();
        } catch (Exception $e) {
            return false;
        }
    }
}
