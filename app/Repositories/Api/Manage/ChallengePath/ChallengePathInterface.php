<?php

namespace App\Repositories\Api\Manage\ChallengePath;

interface ChallengePathInterface
{
    public function uploadChallengePathMedia($image);

    public function uploadAchievementImage($achievementImage);

    public function createChallengePath($upload_cover_image, $upload_achievement_image, $request);
}
