<?php

namespace App\Repositories\Api\Manage\Challenge;

interface ChallengeInterface
{
    public function uploadChallengeCoverImage($image);

    public function createChallenge($request, $upload_cover_image);
}
