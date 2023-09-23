<?php

namespace App\Repositories\Api\Manage\ChallengeSkillsGroupsStack;

use App\Services\Manage\ChallengeSkillsGroupsStackService;
use Exception;

class ChallengeSkillsGroupsStackRepository implements ChallengeSkillsGroupsStackInterface
{
    private $challengeSkillsGroupsStackService;

    public function __construct(ChallengeSkillsGroupsStackService $challengeSkillsGroupsStackService)
    {
        $this->challengeSkillsGroupsStackService = $challengeSkillsGroupsStackService;
    }

    public function createChallengeSkillsGroupsStack($request, $challenge)
    {
        try {
            return $this->challengeSkillsGroupsStackService->createChallengeSkillsGroupsStack($request, $challenge);
        } catch (Exception $th) {
            return false;
        }
    }
}
