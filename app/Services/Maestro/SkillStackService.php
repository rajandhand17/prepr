<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\SkillStack;
use Exception;

class SkillStackService
{
    public static function getSkillStackById($id)
    {
        try {
            $skillStack = SkillStack::find($id);
            if ($skillStack != null) {
                return $skillStack;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function updateSkillStackById($id, $request)
    {
        try {
            $input = $request->all();
            $stack = SkillStack::find($id);
            if ($request->title !== $stack->title && SkillStack::where('title', $request->title)->count() > 0) {
                return redirect()->route('skill-stack.index')->with(['error' => 'Stack title already exists']);
            }
            $languages = LanguageService::getAllActiveLanguages();

            foreach ($languages as $single) {
                $columName1 = UtilityHelper::getColumName($single->iso, 'title');
                $columName2 = UtilityHelper::getColumName($single->iso, 'description');
                $stack->$columName1 = $request->$columName1;
                $stack->$columName2 = $request->$columName2;
            }
            $stack->skills = $request->stack_skills;
            $stack->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteSkillStackById($id)
    {
        try {
            $skillStack = SkillStack::find($id);
            if (!empty($skillStack)) {
                return $skillStack->delete();
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function createSkillStack($request)
    {
        try {
            if (!empty($request->title)) {
                $input = $request->all();

                if (SkillStack::where('title', $request->title)->count() > 0) {
                    return redirect()->route('skill-stack.index')->with(['error' => 'Stack title already exists']);
                }
                $stack = new SkillStack();
                $languages = LanguageService::getAllActiveLanguages();

                foreach ($languages as $single) {
                    $columName1 = UtilityHelper::getColumName($single->iso, 'title');
                    $columName2 = UtilityHelper::getColumName($single->iso, 'description');
                    $stack->$columName1 = $request->$columName1;
                    $stack->$columName2 = $request->$columName2;
                }
                $stack->skills = $request->stack_skills;
                $stack->save();

                return true;
            }

            return redirect()->with('error', 'Enter Skill Stacks');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getSkillStack()
    {
        try {
            return SkillStack::orderBy('id', 'desc');
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getSkillStackBasedOnIds($skillStackIds)
    {
        try {
            $selectedSkills = [];
            foreach ($skillStackIds as $skill) {
                $selectedSkills[] = $skill;
            }
            $getSkillstackList = SkillStack::whereIn('id', $selectedSkills)->pluck('title', 'id')->toArray();
            if ($getSkillstackList) {
                return $getSkillstackList;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getAjaxAllSkillStack($request)
    {
        try {
            $skillsQuery = SkillStack::select('id', 'title')->orderBy('id', 'DESC')->take(30);
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
}
