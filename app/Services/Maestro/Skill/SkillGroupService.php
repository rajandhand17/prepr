<?php

namespace App\Services\Maestro\Skill;

use App\Models\Language;
use App\Models\Skill;
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
            $languages = Language::where('status', 1)->get();

            foreach ($languages as $single) {
                if ($single->iso == 'en') {
                    $columName1 = 'title';
                    $columName2 = 'description';
                } else {
                    $columName = $single->iso;
                    if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                        $columName = str_replace(' ', '_', $columName);
                    }
                    if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                        $columName = str_replace('-', '_', $columName);
                    }
                    $columName1 = $columName.'_title';
                    $columName2 = $columName.'_description';
                }
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

                $languages = Language::where('status', 1)->get();

                foreach ($languages as $single) {
                    if ($single->iso == 'en') {
                        $columName1 = 'title';
                        $columName2 = 'description';
                    } else {
                        $columName = $single->iso;
                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                            $columName = str_replace(' ', '_', $columName);
                        }
                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                            $columName = str_replace('-', '_', $columName);
                        }
                        $columName1 = $columName.'_title';
                        $columName2 = $columName.'_description';
                    }
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

    public static function getSkills()
    {
        try {
            return Skill::orderBy('id', 'desc');
        } catch (Exception $e) {
            return false;
        }
    }
}
