<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Services\Manage\ChallengeAchievementService;
use App\Services\Manage\ChallengeService;
use Exception;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;
    private $challengeAchievementService;

    public function __construct(ChallengeService $challengeService, ChallengeAchievementService $challengeAchievementService)
    {
        $this->challengeService = $challengeService;
        $this->challengeAchievementService = $challengeAchievementService;
    }

    public function uploadChallengeCoverImage($image)
    {
        try {
            return $this->challengeService->uploadChallengeCoverImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createChallenge($request, $upload_cover_image, $upload_achievement_image)
    {
        try {
            $createChallenge = $this->challengeService->createChallenge($request, $upload_cover_image);
            $createChallengeAchievement = $this->challengeAchievementService->createChallengeAchievement($request, $createChallenge, $upload_achievement_image);
            dd($createChallenge, $createChallengeAchievement, 'In Repository');
        } catch (Exception $th) {
            dd($th, 'In Repository');

            return false;
        }
    }
}
