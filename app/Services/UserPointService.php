<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserPointService
{
    public static function getUsersBasedOnPoints(){
        try {
            $userPoints = UserPoint::select('user_id', \DB::raw('COUNT(point) as point_count'))
                ->groupBy('user_id')
                ->orderByDesc('point_count')
                ->pluck('user_id','point_count')
                ->take(20)
                ->toArray();
            $userIds = [$authUserId = auth()->user()->id];

            if (in_array($authUserId, $userPoints)) {
                $userIds = array_merge([$authUserId], array_diff($userPoints, [$authUserId]));
            }

            return $userIds;

        }catch(Exception $e){
            return false;
        }
    }

    public static function getUserPoints($id){
        try {
          $userPoints= UserPoint::select('user_id', \DB::raw('COUNT(*) as point_count'))
                ->groupBy('user_id')
                ->where('user_id',$id)
                ->orderByDesc('point_count')
                ->pluck('point_count');
            return $userPoints;
        }catch(Exception $e){
            return false;
        }
    }
}
