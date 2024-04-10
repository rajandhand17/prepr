<?php

namespace App\Services\Manage;

use App\Models\Go1WebhookMetadata;
use Carbon\Carbon;

class WebhookMetadataService
{
    public static function create($type, $payload, $parentId)
    {
        try {
            return Go1WebhookMetadata::create([
                'type' => $type,
                'fired_at' => Carbon::parse($payload['fired_at'] ?? Carbon::now()),
                'metadata' => $payload,
                'user_resource_progress_tracking_id' => $parentId,
            ]);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
