<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
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
            UtilityHelper::logError($e);
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
                        $columName = UtilityHelper::getColumName($single->iso, 'title');
                        $createArray[$columName] = $request->$columName;
                        $skill->$columName = $request->$columName;
                    }
                    $skill->save();

                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
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
            UtilityHelper::logError($e);
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
                    $columName = UtilityHelper::getColumName($single->iso, 'title');
                    $skill->$columName = $request->$columName;
                }
                $skill->save();

                return redirect()->route('skills.index')->with('success', 'Skill added successfully');
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getSkills()
    {
        try {
            return Skill::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getSkillBasedOnIds($skill_ids)
    {
        try {
            $selectedSkills = [];
            foreach ($skill_ids as $skill) {
                $selectedSkills[] = $skill;
            }
            $getSkillsList = Skill::whereIn('id', $selectedSkills)->pluck('title', 'id')->toArray();
            if ($getSkillsList) {
                return $getSkillsList;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getAjaxAllSkills($request)
    {
        try {
            $skillsQuery = Skill::select('id', 'title')->orderBy('id', 'DESC')->take(30);
            if ($request->search) {
                $skillsQuery->where('title', 'LIKE', '%'.$request->search.'%');
            }
            $skillsQuery = $skillsQuery->pluck('title', 'id');
            $skillsArray = $jsonSkills = [];
            $count = 0;
            foreach ($skillsQuery as $key => $skill) {
                $skillsArray[$count]['id'] = $key;
                $skillsArray[$count]['text'] = $skill;
                $count++;
            }
            $jsonSkills['result'] = $skillsArray;
            $jsonSkills['more'] = true;
            $jsonSkills['total_count'] = $skillsQuery->count();

            return response()->json($jsonSkills);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getSelectedSkillsNameByIds($selectedSkills)
    {
        try {
            $selectedSkills_names = [];
            foreach ($selectedSkills as $skill) {
                if (Skill::where('id', $skill)->get()->count() > 0) {
                    $selectedSkills_names[] = Skill::find($skill)->title;
                } else {
                    return "Skill doesn't exist";
                }
            }
            $selectedSkills_names = implode(', ', $selectedSkills_names);

            return $selectedSkills_names;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getSkillsById($skillIds)
    {
        try {
            return Skill::whereIn('id', $skillIds)->pluck('title', 'id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getSkillsByLanguageId($request)
    {
        try {
            if ($request->language == 'en') {
                $skillsQuery = Skill::select('id', 'title')->orderBy('id', 'DESC')->take(30);
                $columName = 'title';
            } else {
                $columName = $request->language;
                if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                    $columName = str_replace(' ', '_', $columName);
                }
                if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                    $columName = str_replace('-', '_', $columName);
                }
                $columName = $columName.'_title';
                $skillsQuery = Skill::select('id', $columName.' as title')->orderBy('id', 'DESC')->take(30);
            }
            if ($request->search) {
                $skillsQuery->where($columName, 'LIKE', '%'.$request->search.'%');
            }
            $skillsQuery = $skillsQuery->pluck('title', 'id');
            $skillsArray = $jsonSkills = [];
            $count = 0;
            foreach ($skillsQuery as $key => $skill) {
                $skillsArray[$count]['id'] = $key;
                $skillsArray[$count]['text'] = $skill;
                $count++;
            }
            $jsonSkills['result'] = $skillsArray;
            $jsonSkills['more'] = true;
            $jsonSkills['total_count'] = $skillsQuery->count();

            return response()->json($jsonSkills);
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
