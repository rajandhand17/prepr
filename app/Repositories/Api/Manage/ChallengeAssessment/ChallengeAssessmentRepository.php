<?php

namespace App\Repositories\Api\Manage\ChallengeAssessment;

use App\Services\Manage\ChallengeAssessmentService;
use Exception;

class ChallengeAssessmentRepository implements ChallengeAssessmentInterface
{
    private $challengeAssessmentService;

    public function __construct(ChallengeAssessmentService $challengeAssessmentService)
    {
        $this->challengeAssessmentService = $challengeAssessmentService;
    }

    public function createChallengeAssessment($request, $challenge)
    {
        try {
            return $this->challengeAssessmentService->createChallengeAssessment($request, $challenge);
        } catch (Exception $th) {
            return false;
        }
    }
}
