<?php

namespace App\Http\Controllers\Api\Manage\ChallengeTemplate;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateRepository;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Request;

class ChallengeTemplateController extends AppBaseController
{
    private ChallengeTemplateRepository  $challengeTemplateRepository;
    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeTemplateRepository $challengeTemplateRepository,ChallengeRepository $challengeRepository)
    {
        $this->challengeTemplateRepository = $challengeTemplateRepository;
        $this->challengeRepository=$challengeRepository;
    }


    public function create($slug, Request $request)
    {
        try {
            $organization = OrganizationService::getOrganizationExistBasedOnUuid($request->organization_id);
            if (!$organization) {
                return $this->sendError(__('responses.organization_not_found'), 404);
            }
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            $cloneChallenge = $this->challengeTemplateRepository->createTemplateChallenge($checkComponentBasedOnSlug->id, $organization);
            if ($cloneChallenge != false) {
                return $this->sendResponse(ChallengeResource::make($cloneChallenge), __('responses.challenge_clone_success'), 200);
            }
            return $this->sendError(__('responses.challenge_clone_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
