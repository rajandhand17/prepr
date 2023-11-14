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

    public function getAchievementBasedOnCertificateNumber($id)
    {
        try {
            return $this->achievementService->getAchievementBasedOnCertificateNumber($id);
        } catch(\Exception $e) {
            return false;
        }
    }
}
