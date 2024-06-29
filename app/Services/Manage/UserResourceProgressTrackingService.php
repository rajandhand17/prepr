<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\GO1UserResourceProgress;

class UserResourceProgressTrackingService
{
    public static function createOrUpdate($resourceId, $userId, $payload)
    {
        try {
            return GO1UserResourceProgress::query()->updateOrCreate([
                'resource_module_id' => $resourceId,
                'user_id'            => $userId,
            ], [
                'completion_status' => data_get($payload, 'data.status'),
                'lesson_status'     => data_get($payload, 'data.pass') === 1 ? 'pass' : 'fail',
                'score_raw'         => data_get($payload, 'data.result'),
                'session_time'      => data_get($payload, 'data.completed_time'),
            ]);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
