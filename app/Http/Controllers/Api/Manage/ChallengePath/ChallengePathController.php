<?php

namespace App\Http\Controllers\Api\Manage\ChallengePath;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ChallengePath\CreateChallengePathRequest;
use App\Http\Requests\Manage\ChallengePath\UpdateChallengePathRequest;
use App\Http\Resources\Manage\ChallengePath\ChallengePathListNameResource;
use App\Http\Resources\Manage\ChallengePath\ChallengePathResource;
use App\Repositories\Api\Manage\ChallengePath\ChallengePathRepository;
use Exception;
use Illuminate\Http\Request;

class ChallengePathController extends AppBaseController
{
    private $challengePathRepository;

    public function __construct(ChallengePathRepository $challengePathRepository)
    {
        $this->challengePathRepository = $challengePathRepository;
    }

    public function index(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $listChallengePath = $this->challengePathRepository->getChallengePathList($request, $organization);

            if ($listChallengePath) {
                $response = [
                    'total_count'  => $listChallengePath->total(),
                    'per_page'     => $listChallengePath->perPage(),
                    'count'        => $listChallengePath->count(),
                    'current_page' => $listChallengePath->currentPage(),
                    'total_pages'  => $listChallengePath->lastPage(),
                    'list'         => ChallengePathResource::collection($listChallengePath),
                ];

                return $this->sendResponse($response, __('responses.found_challenge_path_list'));
            }

            return $this->sendError(__('responses.not_found_challenge_path_list'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(CreateChallengePathRequest $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            // checks creation limits of the Challenge Path
            $checkChallengePathLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'challengePath');
            if ($checkChallengePathLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkChallengePathCount = $this->challengePathRepository->getChallengePathCountBasedOnOrganization($checkChallengePathLimit['organizationId']);
                if ($checkChallengePathLimit['fetchOrganizationPlanDetails'] <= $checkChallengePathCount) {
                    return $this->sendError(__('responses.reached_challenge_path_limit'), 400);
                }
            }

            $upload_cover_image = config('site-settings.default_challenge_path_cover_image');
            if ($request->media !== null) {
                $uploaded_cover_image = $this->challengePathRepository->uploadChallengePathMedia($request->media);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }

            $upload_achievement_image = config('site-settings.default_challenge_path_profile_image');
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->challengePathRepository->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }

            $createChallengePath = $this->challengePathRepository->createChallengePath($upload_cover_image, $upload_achievement_image, $request, $organization->id);
            if ($createChallengePath != false) {
                return $this->sendResponse(ChallengePathResource::make($createChallengePath), __('responses.challenge_path_stored_success'), 200);
            }

            return $this->sendError(__('responses.challenge_path_stored_failed'), 403);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function update($slug, UpdateChallengePathRequest $request)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengePathRepository->checkSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_path_not_found'), 403);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_path_switcher_error'), 403);
            }
            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_path_not_accessible'), 403);
            }
            $upload_cover_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->media);
            if ($request->media !== null) {
                $uploaded_cover_image = $this->challengePathRepository->uploadChallengePathMedia($request->media);
                if (!$uploaded_cover_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_cover_image = $uploaded_cover_image;
            }
            $upload_achievement_image = config('site-settings.default_challenge_path_profile_image');
            if ($checkComponentBasedOnSlug->achievement != null) {
                $upload_achievement_image = str_replace(config('site-settings.aws_url'), '', $checkComponentBasedOnSlug->achievement->achievement_image);
            }
            if ($request->achievement_image !== null) {
                $uploaded_achievement_image = $this->challengePathRepository->uploadAchievementImage($request->achievement_image);
                if (!$uploaded_achievement_image) {
                    return $this->sendError(__('responses.image_upload_failed'), 400);
                }
                $upload_achievement_image = $uploaded_achievement_image;
            }
            $updateChallengePath = $this->challengePathRepository->updateChallengePath($slug, $request, $upload_cover_image, $upload_achievement_image, $organization->id);
            if ($updateChallengePath != false) {
                return $this->sendResponse(ChallengePathResource::make($updateChallengePath), __('responses.challenge_path_update_successfully'), 200);
            }

            return $this->sendError(__('responses.challenge_path_not_update'), 403);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkSlug($slug)
    {
        try {
            $checkChallengePathSlugExistsOrNot = $this->challengePathRepository->checkSlug($slug);
            if ($checkChallengePathSlugExistsOrNot == false) {
                return $this->sendResponse([], __('responses.challenge_path_slug_available'), 200);
            }

            return $this->sendError(__('responses.challenge_path_already_exists'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function checkName($title)
    {
        try {
            $checkNameChallengePath = $this->challengePathRepository->checkNameExistsOrNot($title);
            if ($checkNameChallengePath == false) {
                return $this->sendResponse([], __('responses.challenge_path_name_available'));
            }

            return $this->sendError(__('responses.challenge_path_name_not_available'), 403);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function delete($slug)
    {
        try {
            $checkChallengePathSlugExistsOrNot = $this->challengePathRepository->checkSlug($slug);
            if ($checkChallengePathSlugExistsOrNot == false) {
                return $this->sendError(__('responses.challenge_path_not_found'), 404);
            }
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            if ($checkChallengePathSlugExistsOrNot->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_path_switcher_error'), 403);
            }
            if ($checkChallengePathSlugExistsOrNot->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_path_not_accessible'), 403);
            }
            $deleteChallengePath = $this->challengePathRepository->delete($checkChallengePathSlugExistsOrNot->id);
            if ($deleteChallengePath) {
                return $this->sendResponse(null, __('responses.challenge_path_delete'));
            }

            return $this->sendError(__('responses.challenge_path_not_delete'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challengePath = $this->challengePathRepository->checkSlug($slug);
            if ($challengePath) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                if (!$organization) {
                    return $this->sendError(__('responses.selected_organization_not_found'), 404);
                }
                if ($challengePath->organization_id != $organization->id) {
                    return $this->sendError(__('responses.challenge_path_switcher_error'), 403);
                }
                if ($challengePath->is_accessible == '0') {
                    return $this->sendError(__('responses.challenge_path_not_accessible'), 403);
                }

                return $this->sendResponse(ChallengePathResource::make($challengePath), __('responses.found_challenge_path_view'));
            }

            return $this->sendError(__('responses.not_found_challenge_path_view'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getList(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $getChallengePathListName = $this->challengePathRepository->getChallengePathListName($request, $organization);
            if ($getChallengePathListName) {
                $response = ChallengePathListNameResource::collection($getChallengePathListName);
            }

            return $this->sendResponse($getChallengePathListName, __('responses.found_challenge_path_list'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
