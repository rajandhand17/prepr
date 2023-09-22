<?php

namespace App\Repositories\Api\Manage\ChallengeSponsor;

use App\Services\Manage\ChallengeSponsorService;
use Exception;

class ChallengeSponsorRepository implements ChallengeSponsorInterface
{
    private $challengeSponsorService;

    public function __construct(ChallengeSponsorService $challengeSponsorService)
    {
        $this->challengeSponsorService = $challengeSponsorService;
    }

    public function createChallengeSponsor($request, $challenge)
    {
        try {
            return $this->challengeSponsorService->createChallengeSponsor($request, $challenge);
        } catch (Exception $th) {
            return false;
        }
    }
}
