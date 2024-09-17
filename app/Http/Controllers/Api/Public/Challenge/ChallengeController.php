<?php

namespace App\Http\Controllers\Api\Public\Challenge;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Public\Challenge\ChallengeListNameResource;
use App\Http\Resources\Public\Challenge\ChallengeProjectRequirementResource;
use App\Http\Resources\Public\Challenge\ChallengeResource;
use App\Repositories\Api\Public\Challenge\ChallengeRepository;
use App\Services\LastVisitedActivityModuleService;
use Exception;
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
        } catch (Exception $e) {
            dd($e);
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if ($challenge) {
                if ($challenge->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_not_accessible'), 403);
                }
                if (auth('api')->check()) {
                    // For user progress tracking
                    $userId = auth('api')->user()->id;
                    TrackUserProgressHelper::trackChallengeUserProgress($challenge, $userId);

                    // For last visited activity tracking
                    $joined_status = $challenge->joined();
                    if ($joined_status != 'NA' && $joined_status != null) {
                        if ($joined_status->invite_status == '1') {
                            $moduleType = config('constants.module_type.challenges');
                            LastVisitedActivityModuleService::lastVisitedActivityModule($challenge->id, $userId, $moduleType);
                        }
                    }
                }

                $this->challengeRepository->incrementView($challenge);

                return $this->sendResponse(ChallengeResource::make($challenge), __('responses.found_challenge_view'));
            }

            return $this->sendError(__('responses.challenge_slug_not_found'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $challenge = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if ($challenge !== null) {
                if ($challenge->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_not_accessible'), 403);
                }
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
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function challengeList(Request $request)
    {
        try {
            $getProjectChallengeList = $this->challengeRepository->getProjectChallenges($request);
            if ($getProjectChallengeList) {
                return $this->sendResponse(ChallengeListNameResource::collection($getProjectChallengeList), __('responses.found_challenges_list'));
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function challengeRequirements($uuid)
    {
        try {
            $fetchChallengeExistsOrNot = $this->challengeRepository->getChallengeBasedOnUUID($uuid);
            if (!$fetchChallengeExistsOrNot) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }
            if ($fetchChallengeExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            $getProjectChallengeRequirement = $this->challengeRepository->getProjectChallengeRequirement($fetchChallengeExistsOrNot);
            if ($getProjectChallengeRequirement) {
                return $this->sendResponse(ChallengeProjectRequirementResource::make($fetchChallengeExistsOrNot), __('responses.project_requirement_found'), 200);
            }

            return $this->sendError(__('responses.project_not_requirement_found'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function projectSubmission($slug, Request $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }

            if ($checkComponentBasedOnSlug->is_accessible === '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            $fetchProjectIdsBasedOnChallenge = $this->challengeRepository->fetchProjectIdsBasedOnChallenge($checkComponentBasedOnSlug->id);
            $fetchProjectIds = $this->challengeRepository->fetchProjectIds($fetchProjectIdsBasedOnChallenge, $request);
            if ($fetchProjectIds !== false) {
                $response = [
                    'total_count'  => $fetchProjectIds->total(),
                    'per_page'     => $fetchProjectIds->perPage(),
                    'count'        => $fetchProjectIds->count(),
                    'current_page' => $fetchProjectIds->currentPage(),
                    'total_pages'  => $fetchProjectIds->lastPage(),
                    'list'         => ProjectResource::collection($fetchProjectIds),
                ];

                return $this->sendResponse($response, __('responses.found_projects_list'));
            }

            return $this->sendError(__('responses.not_found_projects_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
