<?php

namespace App\Repositories\Api\Public\Achievement;

use App\Services\Public\AchievementService;

class AchievementRepository implements AchievementInterface
{
    private $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function getList($request)
    {
        try {
            return $this->achievementService->getList($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getAchievementBasedOnCertificateNumber($certificate_id)
    {
        try {
            return $this->achievementService->getAchievementBasedOnCertificateNumber($certificate_id);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function downloadCertificate($certificate_id)
    {
        try {
            return $this->achievementService->downloadCertificate($certificate_id);
        } catch(\Exception $e) {
            return false;
        }
    }
}
