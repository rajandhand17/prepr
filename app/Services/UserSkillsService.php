<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Helpers\WikipediaHelper;
use App\Jobs\MixpanelJob;
use App\Models\Skill;
use App\Models\UserSkills;
use stdClass;

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
            $profile_data = [
                'type' => 'skills',
                'info' => $inputAllSkills,
            ];

            if (config('app.isMixPanelEnable')) {
                MixpanelJob::dispatch(config('mixpanel.add_skills'), $profile_data, auth()->user(), request()->ip());
            }

            return $allSkills;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkUserSkillExists($id)
    {
        try {
            return UserSkills::where(['skill'=>$id, 'user_id'=>auth('api')->user()->id])->first();
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getMySkills($language = 'en', $search = null, $pinned = null, $sortBy = null)
    {
        try {
            $userSkills = UserSkills::where('user_id', auth()->user()->id)->orderBy('user_skills.pinned', 'desc');
            if (isset($pinned) && $pinned !== null) {
                $checkPinned = ($pinned == 'yes') ? 1 : 0;
                $userSkills = $userSkills->where('pinned', $checkPinned);
            }
            $userSkills = $userSkills->pluck('skill');
            $userSkills = SkillService::getSkills($language, $search, $sortBy, $userSkills);

            return $userSkills;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserSkills()
    {
        try {
            $userSkills = UserSkills::where('user_id', auth()->user()->id)->pluck('skill');

            return $userSkills;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addSingleSkill($request)
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
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addMultipleSkills($skills)
    {
        try {
            $userId = auth()->user()->id;
            foreach ($skills as $value) {
                $existingSkill = UserSkills::where(['user_id' => $userId, 'skill' => $value])->first();
                if (!$existingSkill) {
                    UserSkills::create([
                        'user_id' => $userId,
                        'skill'   => $value,
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addUserSkillsByUsingResumeData($data)
    {
        try {
            if (isset($data['data']['skills']['overall_skills']) && $data['data']['skills']['overall_skills'] !== null) {
                foreach ($data['data']['skills']['overall_skills'] as $skillName) {
                    $skillResponse = WikipediaHelper::fetchRelatedSkills(config('wikipedia.SKILLS_RECOMMENDATION_ENGINE_URL').strtolower($skillName));
                    if (is_array($skillResponse)) {
                        foreach ($skillResponse as $relatedSkillName=>$relatedSkillScore) {
                            if ($relatedSkillScore >= 0.95) {
                                $dbSkill = Skill::where('title', $relatedSkillName)->first();
                                if ($dbSkill) {
                                    $skills = new stdClass();
                                    $skills->skill_id = $dbSkill->id;
                                    $checkSkillExistsOrNot = self::checkUserSkillExists($dbSkill->id);
                                    if ($checkSkillExistsOrNot == null) {
                                        // if ($checkSkillExistsOrNot->count() == 0) {
                                        self::addSingleSkill($skills);
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchUserSkills($userData)
    {
        try {
            $fetchUserSkills = UserSkills::where('user_id', $userData->id)->pluck('skill');
            if (!empty($fetchUserSkills)) {
                return $fetchUserSkills;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function storeVerifySkills($skills,$userId)
    {
        try {
            if (!empty($skills)) {
                $existingSkills = UserSkills::where('user_id', $userId)
                    ->whereIn('skill', $skills)
                    ->get()
                    ->keyBy('skill'); 
    
                $newSkills = [];
    
                foreach ($skills as $skill) {
                    if (isset($existingSkills[$skill])) {
                        $existingSkills[$skill]->is_verified = '1';
                        $existingSkills[$skill]->save();
                    }
                }
            }
    
            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
    
}
