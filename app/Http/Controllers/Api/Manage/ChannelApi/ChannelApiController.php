<?php

namespace App\Http\Controllers\Api\Manage\ChannelApi;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\ChannelApi\AssignUserToLabRequest;
use App\Http\Resources\Manage\ChannelApi\ChallengeResource;
use App\Http\Resources\Manage\ChannelApi\LabResource;
use App\Repositories\Api\Manage\ChannelApi\ChannelApiRepository;
use App\Services\Manage\LabService;
use App\Services\Manage\OrganizationService;
use App\Services\UserService;

class ChannelApiController extends AppBaseController
{
    public function __construct(private ChannelApiRepository $channelApiRepository)
    {
    }

    public function getLabs($type, $id)
    {
        try {
            if (!in_array($type, ['community', 'user'])) {
                return $this->sendError('invalid type', 400);
            }

            $organization = null;
            $user = null;
            if ($type === 'community') {
                $organization = OrganizationService::getOrganizationBasedOnCommunityId($id);
                if (!$organization) {
                    return $this->sendError(__('responses.organization_not_found'), 404);
                }
            }

            if ($type === 'user') {
                $user = UserService::getUserBasedOnMagnetUserId($id);
                if (!$user) {
                    return $this->sendError(__('responses.user_not_found'), 404);
                }
            }

            $labs = $this->channelApiRepository->getLabs($type, $organization, $user);
            $responseData = [
                'total_count' => $labs->total(),
                'per_page' => $labs->perPage(),
                'count' => $labs->count(),
                'current_page' => $labs->currentPage(),
                'total_pages' => $labs->lastPage(),
                'list' => LabResource::collection($labs->items()),
            ];

            return $this->sendResponse($responseData, 'responses.found_labs_list');
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getChallenges($type, $id)
    {
        try {
            if (!in_array($type, ['community', 'user'])) {
                return $this->sendError('invalid type', 400);
            }

            $organization = null;
            $user = null;
            if ($type === 'community') {
                $organization = OrganizationService::getOrganizationBasedOnCommunityId($id);
                if (!$organization) {
                    return $this->sendError(__('responses.organization_not_found'), 404);
                }
            }

            if ($type === 'user') {
                $user = UserService::getUserBasedOnMagnetUserId([$id]);
                if (!$user) {
                    return $this->sendError(__('responses.user_not_found'), 404);
                }
            }
            $challenges = $this->channelApiRepository->getChallenges($type, $organization, $user);
            $responseData = [
                'total_count' => $challenges->total(),
                'per_page' => $challenges->perPage(),
                'count' => $challenges->count(),
                'current_page' => $challenges->currentPage(),
                'total_pages' => $challenges->lastPage(),
                'list' => ChallengeResource::collection($challenges->items()),
            ];

            return $this->sendResponse($responseData, 'responses.found_challenges_list');
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function assignUserToLab(AssignUserToLabRequest $request)
    {
        try {
            $lab = LabService::getLabBasedOnId($request->lab_id);

            if (!$lab) {
                return $this->sendError(__('responses.lab_not_found'), 404);
            }

            $assignToLab = $this->channelApiRepository->assignUserToLab($request->user, $lab);
            if (!$assignToLab) {
                return $this->sendError(__('responses.assign_user_to_lab_failed'), 400);
            }

            return $this->sendResponse($assignToLab, $assignToLab['add_member_response']);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function sso()
    {
        try {
            $magnetOauthServer = UtilityHelper::sanitizeUrl(config('magnet.magnet_oauth_server'));
            $magnetRedirectURI = UtilityHelper::sanitizeUrl(config('site-settings.frontend_site_url')). '/magnet/callback';
            $url = sprintf("%sclient_id=%s&redirect_uri=%s&response_type=code&scope=basic connect employment education lms", $magnetOauthServer, config('magnet.client_id'), $magnetRedirectURI);

            return redirect($url);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
