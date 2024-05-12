<?php

namespace App\Services;

use App\Models\UserPoint;
use Exception;

class UserPointService
{
    public static function getUserPoints($id)
    {
        try {
            $userPoints = UserPoint::where('user_id', $id)->count('point');

            return $userPoints;
        } catch(Exception $e) {
            return false;
        }
    }
}
