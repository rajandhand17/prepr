<?php

namespace App\Http\Controllers\Api\Manage\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Profile\ResourceProfile;
use App\Repositories\Api\Manage\Profile\ProfileRepository;

class ProfileController extends AppBaseController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function userDetails($userName)
    {
        try {
            $responseProfile = $this->profileRepository->userDetails($userName);
            if ($responseProfile) {
                return $this->sendResponse(ResourceProfile::make($responseProfile), __('responses.found_user_profile_detail'));
            }

            return $this->sendError(__('responses.not_found_user_profile_detail'), 404);
        } catch(\Exception $e) {
            return false;
        }
    }
}
