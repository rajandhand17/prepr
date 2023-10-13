<?php

namespace App\Http\Controllers\Api\Public\Challenge;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Repositories\Api\Public\Challenge\ChallengeRepository;
use App\Services\Manage\OrganizationService;
use Illuminate\Http\Request;

class ChallengeController extends AppBaseController
{
    private $challengeRepository;

    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }

    public function index(Request $request)
    {
        try {
            if ($request->organization_id && is_array($request->organization_id)) {
                $organization = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                if (!$organization) {
                    return $this->sendError(__('responses.organization_not_found'), 404);
                }
                $request->merge(['organization_id' => $organization]);
            }
            $challenges = $this->challengeRepository->getList($request);
            if ($challenges !== false) {
                $response = [
                    'total_count'  => $challenges->total(),
                    'per_page'     => $challenges->perPage(),
                    'count'        => $challenges->count(),
                    'current_page' => $challenges->currentPage(),
                    'total_pages'  => $challenges->lastPage(),
                    'list'         => ChallengeResource::collection($challenges),
                ];

                return $this->sendResponse($response, __('responses.found_challenges_list'));
            }

            return $this->sendError(__('responses.not_found_challenges_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if ($challenge) {
                return $this->sendResponse(ChallengeResource::make($challenge), __('responses.found_challenge_view'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if ($challenge !== null) {
                $getColumnNameValue = $this->challengeRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->challengeRepository->checkSocialActivity($challenge->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_challenge'), 400);
                }
                $challenge = $this->challengeRepository->captureSocialActivity($challenge->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($challenge) {
                    return $this->sendResponse([], __('responses.'.$action.'_challenge_successfully'));
                }
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
