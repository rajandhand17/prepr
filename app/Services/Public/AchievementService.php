<?php

namespace App\Services\Public;

use App\Models\UserAchievement;

class AchievementService
{
    public function getList($request)
    {
        try {
            $achievement_list = UserAchievement::select();
            $achievement_list = self::filterAchievementList($request, $achievement_list);

            return $achievement_list->paginate(config('site-settings.pagination_per_page'));
        } catch(\Exception $e) {
            return false;
        }
    }

    public function filterAchievementList($request, $achievement_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $achievement_list = $achievement_list->where('user_achievements.title', $request->search);
            }

            if ($request->has('type') && !empty($request->type)) {
                $achievement_list = $achievement_list->whereIn('user_achievements.achievement_type', $request->type);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $achievement_list->orderBy('user_achievements.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $achievement_list->orderBy('user_achievements.title', 'DESC');
                        break;
                    case 'creation_date':
                        $achievement_list->orderBy('user_achievements.created_at', 'ASC');
                        break;
                    default:
                        $achievement_list->orderBy('user_achievements.id', 'ASC');
                }
            }
            return $achievement_list;
        } catch(\Exception $e) {
            return false;
        }
    }
}
