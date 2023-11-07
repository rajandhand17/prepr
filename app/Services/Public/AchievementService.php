<?php

namespace App\Services\Public;

use App\Models\UserAchievement;

class AchievementService
{

    public function  getList($request){
        try{
            $achievement_list =UserAchievement::select();
            $achievement_list = self::filterAchievementList($request, $achievement_list);

            return $achievement_list->paginate(config('site-settings.pagination_per_page'));

        }catch(\Exception $e){
            return false;
        }
    }

    public function filterAchievementList($request, $achievement_list){
        try{

            if ($request->has('search') && !empty($request->search)) {
                $achievement_list = $achievement_list->whereIN('user_achievements.achievement_type',$request->search);
            }
           return $achievement_list;
        }catch(\Exception $e){
            return false;
        }
    }
}
