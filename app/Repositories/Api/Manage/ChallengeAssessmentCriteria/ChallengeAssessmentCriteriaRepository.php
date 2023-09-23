<?php

namespace App\Repositories\Api\Manage\ChallengeAssessmentCriteria;

use App\Services\Manage\ChallengeAssessmentCriteriaService;
use Exception;

class ChallengeAssessmentCriteriaRepository implements ChallengeAssessmentCriteriaInterface
{
    private $challengeAssessmentCriteriaService;

    public function __construct(ChallengeAssessmentCriteriaService $challengeAssessmentCriteriaService)
    {
        $this->challengeAssessmentCriteriaService = $challengeAssessmentCriteriaService;
    }

    public function createChallengeAssessmentCriteria($request, $challenge)
    {
        try {
            return $this->challengeAssessmentCriteriaService->createChallengeAssessmentCriteria($request, $challenge);
        } catch (Exception $th) {
            return false;
        }
    }
}
