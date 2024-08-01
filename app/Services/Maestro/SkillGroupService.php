<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\SkillGroup;
use Exception;

class SkillGroupService
{
    public static function getSkillGroupById($id)
    {
        try {
            $skillGroup = SkillGroup::find($id);
            if ($skillGroup != null) {
                return $skillGroup;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateSkillGroupById($id, $request)
    {
        try {
            $input = $request->all();
            // $validation_array = [
            //     'title' => 'required|max:255',
            //     'group_skills' => 'required',
            //     'description' => 'required'

            // ];
            // $validation = Validator::make($input, $validation_array);
            // if ($validation->fails()) {
            //     return Redirect::back()->withErrors($validation)->withInput();
            // }
            $group = SkillGroup::find($id);
            if ($request->title !== $group->title && SkillGroup::where('title', $request->title)->count() > 0) {
                return redirect()->route('skillgroup.index')->with(['error' => 'Group title already exists']);
            }
            $languages = LanguageService::getAllActiveLanguages();
            foreach ($languages as $single) {
                $columName1 = UtilityHelper::getColumName($single->iso, 'title');
                $columName2 = UtilityHelper::getColumName($single->iso, 'description');
                $group->$columName1 = $request->$columName1;
                $group->$columName2 = $request->$columName2;
            }
            $group->skills = $request->group_skills;
            $group->skill_stacks = $request->group_stacks;
            $group->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteSkillGroupById($id)
    {
        try {
            $skillGroup = SkillGroup::find($id);
            if (!empty($skillGroup)) {
                return $skillGroup->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createSkillGroup($request)
    {
        try {
            if (!empty($request->title)) {
                $input = $request->all();

                if (SkillGroup::where('title', $request->title)->count() > 0) {
                    return redirect()->route('skillgroup.index')->with(['error' => 'Group title already exists']);
                }
                $group = new SkillGroup();

                $languages = LanguageService::getAllActiveLanguages();

                foreach ($languages as $single) {
                    $columName1 = UtilityHelper::getColumName($single->iso, 'title');
                    $columName2 = UtilityHelper::getColumName($single->iso, 'description');
                    $group->$columName1 = $request->$columName1;
                    $group->$columName2 = $request->$columName2;
                }
                $group->skills = $request->group_skills;
                $group->skill_stacks = $request->group_stacks;
                $group->save();

                return redirect()->route('skillgroup.index')->with('success', 'Skill Group added successfully');
            }

            return redirect()->with('error', 'Enter Skill Groups');
        } catch (Exception $e) {
            return redirect()->route('skillgroup.index')->with(['error' => $e->getMessage()]);
        }
    }

    public static function getSkillGroup()
    {
        try {
            return SkillGroup::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
