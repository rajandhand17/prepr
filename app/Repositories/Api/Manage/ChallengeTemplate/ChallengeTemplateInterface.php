<?php

namespace App\Repositories\Api\Manage\ChallengeTemplate;

interface ChallengeTemplateInterface
{
    public function createTemplateChallenge($challengeId, $organization);
}
