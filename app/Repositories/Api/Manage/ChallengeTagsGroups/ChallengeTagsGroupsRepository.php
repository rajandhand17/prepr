<?php

namespace App\Repositories\Api\Manage\ChallengeTagsGroups;

use App\Services\Manage\ChallengeTagsGroupsService;
use Exception;

class ChallengeTagsGroupsRepository implements ChallengeTagsGroupsInterface
{
    private $challengeTagsGroupsService;

    public function __construct(ChallengeTagsGroupsService $challengeTagsGroupsService)
    {
        $this->challengeTagsGroupsService = $challengeTagsGroupsService;
    }

    public function createChallengeTagsGroups($request, $challenge)
    {
        try {
            return $this->challengeTagsGroupsService->createChallengeTagsGroups($request, $challenge);
        } catch (Exception $th) {
            return false;
        }
    }
}
