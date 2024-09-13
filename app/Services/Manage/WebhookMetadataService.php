<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\GO1WebhookMetadata;
use Carbon\Carbon;

class WebhookMetadataService
{
    public static function create($type, $payload, $parentId)
    {
        try {
            return GO1WebhookMetadata::create([
                'type'                          => $type,
                'fired_at'                      => Carbon::parse($payload['fired_at'] ?? Carbon::now()),
                'metadata'                      => $payload,
                'go1_user_resource_progress_id' => $parentId,
            ]);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
