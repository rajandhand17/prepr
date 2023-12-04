<?php

namespace App\Http\Controllers\Api\Manage\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Profile\AddEducationRequest;
use App\Http\Requests\Manage\Profile\AddPersonalDetailRequest;
use App\Http\Resources\Manage\Profile\AddEducationResource;
use App\Http\Resources\Manage\Profile\AddPersonalDetailResource;
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
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addPersonalDetail(AddPersonalDetailRequest $request)
    {
        try {
            $addProfile = $this->profileRepository->addPersonalDetail($request);
            if ($addProfile) {
                return $this->sendResponse(AddPersonalDetailResource::make($addProfile), __('responses.user_personal_created'));
            }

            return $this->sendError(__('responses.user_personal_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addEducation(AddEducationRequest $request)
    {
        try {
            $addEducation = $this->profileRepository->addEducation($request);
            if ($addEducation) {
                return $this->sendResponse(AddEducationResource::make($addEducation), __('responses.user_education_created'));
            }
            return $this->sendError(__('responses.user_education_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
