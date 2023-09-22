<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Services\Manage\ChallengeService;
use Exception;

class ChallengeRepository implements ChallengeInterface
{
    private $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }

    public function uploadChallengeCoverImage($image)
    {
        try {
            return $this->challengeService->uploadChallengeCoverImage($image);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createChallenge($request)
    {
        try {
            $createChallenge = $this->challengeService->createChallenge($request);
            dd($createChallenge, "In Repository");
        } catch (Exception $th) {
            dd($th, "In Repository");
            return false;
        }
    }
}
