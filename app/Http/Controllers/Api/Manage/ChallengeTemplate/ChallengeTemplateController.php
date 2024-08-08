<?php

namespace App\Http\Controllers\Api\Manage\ChallengeTemplate;

use App\Helpers\ChargebeeHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Challenge\ChallengeResource;
use App\Http\Resources\Manage\ChallengeTemplate\ChallengeTemplateResource;
use App\Repositories\Api\Manage\Challenge\ChallengeRepository;
use App\Repositories\Api\Manage\ChallengeTemplate\ChallengeTemplateRepository;
use Exception;
use Illuminate\Http\Request;

class ChallengeTemplateController extends AppBaseController
{
    private ChallengeTemplateRepository  $challengeTemplateRepository;
    private ChallengeRepository $challengeRepository;

    public function __construct(ChallengeTemplateRepository $challengeTemplateRepository, ChallengeRepository $challengeRepository)
    {
        $this->challengeTemplateRepository = $challengeTemplateRepository;
        $this->challengeRepository = $challengeRepository;
    }

    public function index(Request $request)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }
            $request->merge(['organization_id' => $organization->id]);

            $challengeTemplate = $this->challengeTemplateRepository->getChallengeTemplateList($request);
            if ($challengeTemplate) {
                $response = [
                    'total_count'  => $challengeTemplate->total(),
                    'per_page'     => $challengeTemplate->perPage(),
                    'count'        => $challengeTemplate->count(),
                    'current_page' => $challengeTemplate->currentPage(),
                    'total_pages'  => $challengeTemplate->lastPage(),
                    'list'         => ChallengeTemplateResource::collection($challengeTemplate),
                ];

                return $this->sendResponse($response, __('responses.found_challenge_templates_list'));
            }

            return $this->sendError(__('responses.not_found_challenge_templates_list'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addChallengeToTemplate($slug)
    {
        try {
            $checkComponentBasedOnSlug = $this->challengeRepository->getChallengeBasedOnSlug($slug);
            if (!$checkComponentBasedOnSlug) {
                return $this->sendError(__('responses.challenge_not_found'), 403);
            }

            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            if ($checkComponentBasedOnSlug->organization_id != $organization->id) {
                return $this->sendError(__('responses.challenge_switcher_error'), 403);
            }

            if ($checkComponentBasedOnSlug->is_accessible == '0') {
                return $this->sendError(__('responses.challenge_not_accessible'), 403);
            }

            $checkChallengeTemplate = $this->challengeTemplateRepository->getCheckChallengeUuid($checkComponentBasedOnSlug->uuid);
            if ($checkChallengeTemplate) {
                return $this->sendError(__('responses.challenge_already_cloned'), 422);
            }

            $addChallengeTemplate = $this->challengeTemplateRepository->addChallengeToTemplate($checkComponentBasedOnSlug->id);
            if ($addChallengeTemplate != false) {
                return $this->sendResponse(ChallengeTemplateResource::make($addChallengeTemplate), __('responses.challenge_add_template_success'), 200);
            }

            return $this->sendError(__('responses.challenge_clone_failed'), 400);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function show($slug)
    {
        try {
            $challengeTemplate = $this->challengeTemplateRepository->getChallengeTemplateBasedOnSlug($slug);
            if ($challengeTemplate) {
                return $this->sendResponse(ChallengeTemplateResource::make($challengeTemplate), __('responses.found_challenge_detail'), 200);
            }

            return $this->sendError(__('responses.found_not_challenge_detail'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function redeemChallenge($slug)
    {
        try {
            $userData = auth()->user();
            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
            if (!$organization) {
                return $this->sendError(__('responses.selected_organization_not_found'), 404);
            }

            $challengeTemplate = $this->challengeTemplateRepository->getChallengeTemplateBasedOnSlug($slug);
            if (!$challengeTemplate) {
                return $this->sendError(__('responses.challenge_template_not_found'), 404);
            }

            $checkChallengeRedeemedOrNot = $this->challengeTemplateRepository->checkChallengeRedeemedOrNot($challengeTemplate->id, $organization->id);
            if (!$checkChallengeRedeemedOrNot) {
                return $this->sendError(__('responses.challenge_template_already_redeemed'), 404);
            }
            // checks creation limits of the Challenge
            $checkChallengeLimit = ChargebeeHelper::checkComponentLimitBasedOnOrganization($organization->id, 'challenge');
            if ($checkChallengeLimit['fetchOrganizationPlanDetails'] !== 'Unlimited') {
                $checkChallengeCount = $this->challengeRepository->getChallengeCountBasedOnOrganization($organization->id);
                if ($checkChallengeLimit['fetchOrganizationPlanDetails'] <= $checkChallengeCount) {
                    return $this->sendError(__('responses.reached_challenge_limit'), 400);
                }
            }
            $challengeRedeem = $this->challengeTemplateRepository->challengeRedeem($challengeTemplate->id, $organization->id);
            if ($challengeRedeem) {
                return $this->sendResponse(ChallengeResource::make($challengeRedeem), __('responses.challenge_template_redeemed'), 200);
            }

            return $this->sendError(__('responses.challenge_template_not_redeemed'), 404);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteChallengeTemplate($slug)
    {
        try {
            $challengeTemplate = $this->challengeTemplateRepository->getChallengeTemplateBasedOnSlug($slug);
            if (!$challengeTemplate) {
                return $this->sendError(__('responses.challenge_template_not_found'), 404);
            }

            $deleteChallengeTemplate = $this->challengeTemplateRepository->deleteChallengeTemplate($slug, $challengeTemplate->id);
            if ($deleteChallengeTemplate) {
                return $this->sendResponse(null, __('responses.challenge_template_deleted_successfully'));
            }

            return $this->sendError(__('responses.challenge_template_deleted_failed'), 402);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
