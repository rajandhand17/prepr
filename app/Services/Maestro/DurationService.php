<?php

namespace App\Services\Maestro;

use App\Models\Duration;
use Exception;

class DurationService
{
    public static function getLevelById($duration_id)
    {
        try {
            return Duration::where(['id' => $duration_id])->pluck('title', 'id');
        } catch(Exception $e) {
            return false;
        }
    }
}
