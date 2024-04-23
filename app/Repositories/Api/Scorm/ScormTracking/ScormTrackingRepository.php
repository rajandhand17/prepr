<?php

namespace App\Repositories\Api\Scorm\ScormTracking;

use App\Services\Scorm\ScormScoTrackingService;

class ScormTrackingRepository implements ScormTrackingInterface
{
    public function __construct(protected ScormScoTrackingService $scormTrackingService)
    {
    }

    public function store(int $userId, string $scoUUID, string $version, array $data)
    {
        try {
            return $this->scormTrackingService->store($userId, $scoUUID, $version, $data);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
