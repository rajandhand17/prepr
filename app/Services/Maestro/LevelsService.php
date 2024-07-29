<?php

namespace App\Services\Maestro;

use App\Models\Levels;
use Exception;

class LevelsService
{
    public static function getLevelById($level_id)
    {
        try {
            return Levels::where(['id' => $level_id])->pluck('title', 'id');
        } catch(Exception $e) {
            return false;
        }
    }
}
