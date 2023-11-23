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

    public function getAchievementBasedOnCertificateNumber($certificateNumber)
    {
        try {
            return $this->achievementService->getAchievementBasedOnCertificateNumber($certificateNumber);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function downloadCertificate($certificate_number, $type)
    {
        try {
            return $this->achievementService->downloadCertificate($certificate_number, $type);
        } catch(\Exception $e) {
            return false;
        }
    }
}
