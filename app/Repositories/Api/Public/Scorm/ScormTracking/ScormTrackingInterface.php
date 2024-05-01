<?php

namespace App\Repositories\Api\Public\Scorm\ScormTracking;

interface ScormTrackingInterface
{
    /**
     * @param int    $userId
     * @param string $scoUUID
     * @param string $version
     * @param array  $data
     *
     * @return mixed
     */
    public function store(int $userId, string $scoUUID, string $version, array $data);
}
