<?php

namespace App\Repositories\Api\Manage\ChallengeRequirement;

interface ChallengeRequirementInterface
{
    public function createChallengeRequirement($request, $challenge);
}
