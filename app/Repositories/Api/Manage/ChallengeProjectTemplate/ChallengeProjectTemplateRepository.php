<?php

namespace App\Repositories\Api\Manage\ChallengeProjectTemplate;

use App\Services\Manage\ChallengeProjectTemplateService;
use Exception;

class ChallengeProjectTemplateRepository implements ChallengeProjectTemplateInterface
{
    private $challengeProjectTemplateService;

    public function __construct(ChallengeProjectTemplateService $challengeProjectTemplateService)
    {
        $this->challengeProjectTemplateService = $challengeProjectTemplateService;
    }

    public function createChallengeProjectTemplate($request, $challenge)
    {
        try {
            return $this->challengeProjectTemplateService->createChallengeProjectTemplate($request, $challenge);
        } catch (Exception $th) {
            return false;
        }
    }
}
