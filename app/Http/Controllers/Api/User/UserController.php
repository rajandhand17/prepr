<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Organization\OnboardingOrganizationRequest;
use App\Http\Resources\Public\Organization\OrganizationDetailResource;
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

    public function getOrganizationList(Request $request)
    {
        try {
            $organizationListing = $this->userRepository->organizationListing($request);
            if ($organizationListing != false) {
                $response = [
                    'total_count'  => $organizationListing->total(),
                    'per_page'     => $organizationListing->perPage(),
                    'count'        => $organizationListing->count(),
                    'current_page' => $organizationListing->currentPage(),
                    'total_pages'  => $organizationListing->lastPage(),
                    'list'         => UserOrganizationListResource::collection($organizationListing),
                ];

                return $this->sendResponse($response, __('responses.found_organization_list'));
            }

            return $this->sendError(__('responses.found_organization_list'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function organizationPreference(Request $request, $slug = null)
    {
        try {
            if ($request->isMethod('post') && $slug != null) {
                $checkComponentSlugExistOrNot = UtilityHelper::checkComponentSlugExistOrNot('organization', $slug);
                if ($checkComponentSlugExistOrNot) {
                    $setOrganizationPreference = $this->userRepository->setOrganizationPreference($checkComponentSlugExistOrNot->id);

                    return $this->sendResponse(UserResource::make(auth()->user()), __('responses.preferred_organization_updated'));
                }
            } elseif ($request->isMethod('get') && $slug == null) {
                $userData = auth()->user();
                $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                if ($organization) {
                    $organization_details['id'] = $organization->uuid;
                    $organization_details['title'] = $organization->title;
                    $organization_details['slug'] = $organization->slug;

                    return $this->sendResponse($organization_details, __('responses.selected_organization_found'));
                }
            }

            return $this->sendError(__('responses.selected_organization_not_found'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function completeBoarding(OnboardingOrganizationRequest $request, $slug = null)
    {
        try {
            if ($slug != null) {
                $checkComponentSlugExistOrNot = UtilityHelper::checkComponentSlugExistOrNot('organization', $slug);
                if ($checkComponentSlugExistOrNot->is_onboarding_completed == '1') {
                    return $this->sendError(__('responses.already_organization_onboarding_completed'), 400);
                }
                $organizationOnboarding = $this->userRepository->organizationOnboarding($checkComponentSlugExistOrNot->id, $request);
                if ($organizationOnboarding) {
                    return $this->sendResponse(OrganizationDetailResource::make($checkComponentSlugExistOrNot), __('responses.organization_onboarding_completed'));
                }

                return $this->sendError(__('responses.organization_onboarding_not_completed'), 404);
            } else {
                $userData = auth()->user();
                if ($userData->is_onboarding_completed == '1') {
                    return $this->sendError(__('responses.already_user_onboarding_completed'), 400);
                }

                $userOnboarding = $this->userRepository->userOnboarding();
                if ($userOnboarding) {
                    return $this->sendResponse(UserResource::make($userData), __('responses.user_onboarding_completed'));
                }
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
