<?php

namespace App\Services\Maestro;

use App\Helpers\Maestro\UtilityHelper;
use App\Models\Language;
use App\Models\Skill;
use Exception;

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
                    $languages = LanguageService::getAllActiveLanguages();
                    $createArray = [];

                    foreach ($languages as $single) {
                        $columName = UtilityHelper::getColumName($single->iso,'title');
                        $createArray[$columName] = $request->$columName;
                        $skill->$columName = $request->$columName;
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
                $languages = LanguageService::getAllActiveLanguages();
                $skill = new Skill();
                foreach ($languages as $single) {
                    $columName = UtilityHelper::getColumName($single->iso,'title');
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
