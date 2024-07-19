<?php

namespace App\Services\Maestro\Skill;

use App\Models\Language;
use App\Models\Skill;
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
            return false;
        }
    }

    public static function updateSkillStackById($id, $request)
    {
        try {
            $input = $request->all();
            // $validation_array = [
            //     'title' => 'required|max:255',
            //     'stack_skills' => 'required',
            //     'description' => 'required'

            // ];
            // $validation = Validator::make($input, $validation_array);
            // if ($validation->fails()) {
            //     return Redirect::back()->withErrors($validation)->withInput();
            // }
            $stack = SkillStack::find($id);
            if ($request->title !== $stack->title && SkillStack::where('title', $request->title)->count() > 0) {
                return redirect()->route('skillstack.index')->with(['error' => 'Stack title already exists']);
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
                $stack->$columName1 = $request->$columName1;
                $stack->$columName2 = $request->$columName2;
            }
            $stack->skills = $request->stack_skills;
            $stack->save();

            return true;
        } catch (Exception $e) {
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
            return false;
        }
    }

    public static function createSkillStack($request)
    {
        try {
            if (!empty($request->title)) {
                $input = $request->all();

                if (SkillStack::where('title', $request->title)->count() > 0) {
                    return redirect()->route('skillstack.index')->with(['error' => 'Stack title already exists']);
                }
                $stack = new SkillStack();

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
                    $stack->$columName1 = $request->$columName1;
                    $stack->$columName2 = $request->$columName2;
                }
                $stack->skills = $request->stack_skills;
                $stack->save();

                return redirect()->route('skillstack.index')->with('success', 'Skill Stack added successfully');
            }

            return redirect()->with('error', 'Enter Skill Stacks');
        } catch (Exception $e) {
            return redirect()->route('skillstack.index')->with(['error' => $e->getMessage()]);
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
