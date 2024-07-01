<?php

namespace App\Http\Controllers\Api\Public\ChallengePath;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Public\ChallengePath\ChallengePathResource;
use App\Repositories\Api\Public\ChallengePath\ChallengePathRepository;
use Exception;
use Illuminate\Http\Request;

class ChallengePathController extends AppBaseController
{
    protected $challengePathRepository;

    public function __construct(ChallengePathRepository $challengePathRepository)
    {
        $this->challengePathRepository = $challengePathRepository;
    }

    public function index(Request $request)
    {
        try {
            $challengePath = $this->challengePathRepository->getList($request);
            if ($challengePath !== false) {
                $response = [
                    'total_count'  => $challengePath->total(),
                    'per_page'     => $challengePath->perPage(),
                    'count'        => $challengePath->count(),
                    'current_page' => $challengePath->currentPage(),
                    'total_pages'  => $challengePath->lastPage(),
                    'list'         => ChallengePathResource::collection($challengePath),
                ];

                return $this->sendResponse($response, __('responses.found_challenge_path_list'));
            }

            return $this->sendError(__('responses.not_found_challenge_path_list'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challengePath = $this->challengePathRepository->getChallengePathBasedOnSlug($slug);
            if ($challengePath) {
                if ($challengePath->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_path_not_accessible'), 403);
                }

                return $this->sendResponse(ChallengePathResource::make($challengePath), __('responses.found_challenge_path_view'));
            }

            return $this->sendError(__('responses.challenge_path_not_found'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function socialActivity($slug, $action)
    {
        try {
            $challengePath = $this->challengePathRepository->getChallengePathBasedOnSlug($slug);
            if ($challengePath !== null) {
                if ($challengePath->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_path_not_accessible'), 403);
                }
                $getColumnNameValue = $this->challengePathRepository->getColumnNameValue($action);
                if (!$getColumnNameValue) {
                    return $this->sendError(__('responses.handler_bad_request'), 400);
                }
                $checkActivity = $this->challengePathRepository->checkSocialActivity($challengePath->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                $action = str_replace('-', '_', $action);
                if ($checkActivity === true) {
                    return $this->sendError(__('responses.already_'.$action.'_challenge_path'), 400);
                }
                $challengePath = $this->challengePathRepository->captureSocialActivity($challengePath->id, $getColumnNameValue['column'], $getColumnNameValue['action']);
                if ($challengePath) {
                    return $this->sendResponse([], __('responses.'.$action.'_challenge_path_successfully'));
                }
            }

            return $this->sendError(__('responses.challenge_path_not_found'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
