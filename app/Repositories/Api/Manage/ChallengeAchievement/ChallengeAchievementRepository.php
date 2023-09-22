<?php

namespace App\Repositories\Api\Manage\ChallengeAchievement;

use App\Services\Manage\ChallengeAchievementService;
use Exception;

class ChallengeAchievementRepository implements ChallengeAchievementInterface
{
    private $challengeAchievementService;

    public function __construct(ChallengeAchievementService $challengeAchievementService)
    {
        $this->challengeAchievementService = $challengeAchievementService;
    }

    public function uploadChallengeAchievementImage($image)
    {
        try {
            return $this->challengeAchievementService->uploadChallengeAchievementImage($image);
        } catch (Exception $th) {
            return false;
        }
    }
}
