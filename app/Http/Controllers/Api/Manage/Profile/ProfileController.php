<?php

namespace App\Http\Controllers\Api\Manage\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\Profile\ProfileResource;
use App\Repositories\Api\Manage\Profile\ProfileRepository;

class ProfileController extends AppBaseController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function show($user_name)
    {
        try {
            $responseProfile = $this->profileRepository->getProfileBasedOnUserId($user_name);
            if ($responseProfile) {
                return $this->sendResponse(ProfileResource::make($responseProfile), __('responses.found_user_profile_detail'));
            }

            return $this->sendError(__('responses.not_found_user_profile_detail'), 404);
        } catch(\Exception $e) {
            return false;
        }
    }
}
