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
            $getProfile = $this->profileRepository->getProfileBasedOnUserName($user_name);
            if ($getProfile) {
                return $this->sendResponse(ProfileResource::make($getProfile), __('responses.found_user_profile_detail'));
            }
            return $this->sendError(__('responses.not_found_user_profile_detail'), 404);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addExperience(Request $request)
    {
        try {
            $getExperience=$this->profileRepository->addExperience($request);
            if($getExperience){
                return $this->sendResponse(ProfileResource::make($getExperience), __(''));
            }
        }catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
