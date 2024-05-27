<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\User\UserOrganizationListResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserSearchResource;
use App\Repositories\Api\User\UserRepository;
use Illuminate\Http\Request;

class UserController extends AppBaseController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        try {
            $userListing = $this->userRepository->getUsers($request);
            if ($userListing != false) {
                return $this->sendResponse(UserSearchResource::collection($userListing), __('responses.found_user_list'));
            }

            return $this->sendError(__('responses.found_user_list'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getLoggedinUser()
    {
        try {
            return $this->sendResponse(UserResource::make(auth()->user()), __('responses.found_user_profile_detail'));
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getOrganizationList()
    {
        try {
            $organizationListing = $this->userRepository->organizationListing();
            if ($organizationListing != false) {
                return $this->sendResponse(UserOrganizationListResource::collection($organizationListing), __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.found_organization_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function setOrganizationPreference($slug)
    {
        try {
            $checkComponentSlugExistOrNot = UtilityHelper::checkComponentSlugExistOrNot('organization', $slug);
            if ($checkComponentSlugExistOrNot) {
                $setOrganizationPreference = $this->userRepository->setOrganizationPreference($checkComponentSlugExistOrNot->id);

                return $this->sendResponse(UserResource::make(auth()->user()), __('responses.preferred_organization_updated'));
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
