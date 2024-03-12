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

    public static function getMySkills($language='en', $search=null, $pinned=null)
    {
        try {
            $userSkills = UserSkills::where('user_id', auth()->user()->id)->orderBy('user_skills.pinned', 'desc');
            if (isset($pinned) && $pinned !== null) {
                $checkPinned = ($pinned == 'yes') ? 1 : 0;
                $userSkills = $userSkills->where('pinned', $checkPinned);
            }
            $userSkills = $userSkills->pluck('skill');
            $userSkills = SkillService::getSkills($language, $search, $sortBy = null, $userSkills);

            return $userSkills;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function getUserSkills()
    {
        try {
            $userSkills = UserSkills::where('user_id', auth()->user()->id)->pluck('skill');
            return $userSkills;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addSingleSkill($request)
    {
        try {
            $checkExisitngSKills = UserSkills::where(['user_id' => auth()->user()->id, 'skill'=>$request->skill_id])->first();
            if (!$checkExisitngSKills) {
                $addSkill = UserSkills::create([
                    'user_id' => auth()->user()->id,
                    'skill'   => $request->skill_id,
                ]);

                return $addSkill;
            } else {
                return 'already';
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addSkillPinned($request)
    {
        try {
            $pinned = $request->pinned == 'yes' ? '1' : '0';
            $userSkill = UserSkills::where(['user_id'=>auth()->user()->id, 'skill'=>$request->skill_id])->first();
            if ($userSkill) {
                $userSkill->pinned = $pinned;
                $userSkill->save();
            } else {
                $userSkill = UserSkills::create([
                    'user_id' => auth()->user()->id,
                    'skill'   => $request->skill_id,
                    'pinned'  => $pinned,
                ]);
            }

            return $userSkill;
        } catch(\Exception $e) {
            return false;
        }
    }
}
