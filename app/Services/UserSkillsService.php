<?php

namespace App\Services;

use App\Models\UserSkills;

class UserSkillsService
{
    public function addSkills($request)
    {
        try {
            $deleteSKills = UserSkills::where(['user_id' => auth()->user()->id])->delete();
            $inputAllSkills = $request->all();
            $allSkills = [];

            foreach ($inputAllSkills['skill_id'] as $key => $value) {
                $addSkill = UserSkills::create([
                    'user_id' => auth()->user()->id,
                    'skill'   => $value,
                    'pinned'  => $inputAllSkills['pinned'][$key],
                ]);

                $allSkills[] = $addSkill;
            }

            return $allSkills;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteSkill($id)
    {
        try {
            $deleteSkill = UserSkills::where('id', $id)->delete();
            if ($deleteSkill) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function checkUserSkillExists($id)
    {
        try {
            return UserSkills::where('id', $id)->first();
        } catch(\Exception $e) {
            return false;
        }
    }
}
