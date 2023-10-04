<?php

namespace App\Repositories\Api\Manage\ChallengePath;

interface ChallengePathInterface
{
    public function uploadChallengePathMedia($image);

    public function uploadAchievementImage($achievementImage);
}
