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
                $checkExisitngSKills = UserSkills::where(['user_id' => auth()->user()->id, 'skill'=>$value])->first();
                if (!$checkExisitngSKills) {
                    $addSkill = UserSkills::create([
                        'user_id' => auth()->user()->id,
                        'skill'   => $value,
                        'pinned'  => $inputAllSkills['pinned'][$key],
                    ]);
                    $allSkills[] = $addSkill;
                }
            }

            return $allSkills;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteProfileSkill($id)
    {
        try {
            $deleteSkill = UserSkills::where(['skill'=>$id, 'user_id'=>auth()->user()->id])->delete();
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
            return UserSkills::where(['skill'=>$id, 'user_id'=>auth()->user()->id])->first();
        } catch(\Exception $e) {
            return false;
        }
    }
}
