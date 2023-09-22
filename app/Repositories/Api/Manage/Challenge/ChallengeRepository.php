<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Services\Manage\ChallengeAchievementService;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ChallengeSponsorService;
use Exception;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;
    private $challengeAchievementService;
    private $challengeSponsorService;

    public function __construct(ChallengeService $challengeService, ChallengeAchievementService $challengeAchievementService, ChallengeSponsorService $challengeSponsorService)
    {
        $this->challengeService = $challengeService;
        $this->challengeAchievementService = $challengeAchievementService;
        $this->challengeSponsorService = $challengeSponsorService;
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
            $createChallengeAchievement = $this->challengeAchievementService->createChallengeAchievement($request, $createChallenge->id, $upload_achievement_image);
            $createChallengeSponsor = $this->challengeSponsorService->createChallengeSponsor($request, $createChallenge->id, $upload_achievement_image);
            dd($createChallenge, $createChallengeAchievement, $createChallengeSponsor, 'In Repository');
        } catch (Exception $th) {
            dd($th, 'In Repository');

            return false;
        }
    }
}
