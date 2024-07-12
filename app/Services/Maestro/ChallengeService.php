<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use Exception;

class ChallengeService
{
    public static function getChallengeBasedOnId($id)
    {
        try {
            return Challenge::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
