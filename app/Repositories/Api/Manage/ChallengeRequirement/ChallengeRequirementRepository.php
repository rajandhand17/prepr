<?php

namespace App\Repositories\Api\Manage\ChallengeRequirement;

use App\Services\Manage\ChallengeRequirementService;
use Exception;

class ChallengeRequirementRepository implements ChallengeRequirementInterface
{
    private $ChallengeRequirementService;

    public function __construct(ChallengeRequirementService $ChallengeRequirementService)
    {
        $this->ChallengeRequirementService = $ChallengeRequirementService;
    }

    public function createChallengeRequirement($request, $challenge)
    {
        try {
            return $this->ChallengeRequirementService->createChallengeRequirement($request, $challenge);
        } catch (Exception $th) {
            return false;
        }
    }
}
