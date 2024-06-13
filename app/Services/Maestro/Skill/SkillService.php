<?php

namespace App\Services\Maestro\Skill;

use App\Models\Language;
use App\Models\Skill;
use Exception;
use Illuminate\Support\Facades\Hash;

class SkillService
{
    public static function getSkillById($id)
    {
        try {
            $skill = Skill::find($id);
            if ($skill != null) {
                return $skill;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function updateSkillById($id, $request)
    {
        try {
            $skill = Skill::find($id);
            if ($skill !== null) {
                if (!empty($request->skill)) {
                    $languages = Language::where('status', 1)->get();
                    $createArray = [];

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
                            $columName = $columName . '_title';
                        }
                        $createArray [$columName] =  $request->$columName;
                        $skill->$columName= $request->$columName;
                    }
                    $skill->save();
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function deleteSkill($id)
    {
        try {
            $skill = Skill::find($id);
            if (!empty($skill)) {
                return $skill->delete();
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function createSkill($request)
    {
        try {
            if (!empty($request->title)) {
                $languages = Language::where('status', 1)->get();
                $skill = new Skill;
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
                        $columName = $columName . '_title';
                    }
                    $skill->$columName = $request->$columName;
                }
                $skill->save();
                return redirect()->route('skills.index')->with('success', 'Skill added successfully');
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    public static function getSkills()
    {
        try {
            return Skill::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
