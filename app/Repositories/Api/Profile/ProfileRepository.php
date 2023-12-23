<?php

namespace App\Repositories\Api\Profile;

use App\Services\ProfileService;

class ProfileRepository implements ProfileInterface
{
    private $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function getProfileBasedOnUserName($user_name)
    {
        try {
            return $this->profileService->getProfileBasedOnUserName($user_name);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addPersonalDetail($request)
    {
        try {
            return $this->profileService->addPersonalDetail($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addUserExperience($request)
    {
        try {
            return $this->profileService->addUserExperience($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteUserExperience($id){
        try {
            return $this->profileService->deleteUserExperience($id);

        }catch (\Exception $e) {
            return false;
        }
    }
    public function addEducation($request)
    {
        try {
            return $this->profileService->addEducation($request);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function deleteEducation($id){
        try {
            return $this->profileService->deleteEducation($id);
        }catch (\Exception $e) {
            return false;
        }
    }
    public function addPatent($request)
    {
        try {
            return $this->profileService->addPatent($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addSkills($request)
    {
        try {
            return $this->profileService->addSkills($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addCertificate($request)
    {
        try {
            return $this->profileService->addCertificate($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteSkill($id)
    {
        try {
            return $this->profileService->deleteSkill($id);
        } catch (\Exception $e) {
            return false;
        }
    }
}
