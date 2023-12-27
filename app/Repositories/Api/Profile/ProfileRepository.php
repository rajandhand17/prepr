<?php

namespace App\Repositories\Api\Profile;

use App\Services\UserCertificateService;
use App\Services\UserEducationService;
use App\Services\UserExperienceService;
use App\Services\UserPatentService;
use App\Services\UserPersonalService;
use App\Services\UserService;
use App\Services\UserSkillsService;

class ProfileRepository implements ProfileInterface
{
    private $userService;

    private $userPersonalService;

    private $userExperienceService;

    private $userEducationService;

    private $userSkillsService;

    private $userPatentsService;

    private $userCertificatesService;


    public function __construct(UserCertificateService $userCertificatesService, UserPatentService $userPatentsService,UserSkillsService $userSkillsService, UserService $userService, UserPersonalService $userPersonalService,UserExperienceService $userExperienceService,UserEducationService $userEducationService)
    {
        $this->userService = $userService;
        $this->userPersonalService = $userPersonalService;
        $this->userExperienceService = $userExperienceService;
        $this->userEducationService = $userEducationService;
        $this->userSkillsService=$userSkillsService;
        $this->userPatentsService=$userPatentsService;
        $this->userCertificatesService=$userCertificatesService;
    }

    public function getUserByUsername($user_name)
    {
        try {
            return $this->userService->getUserByUsername($user_name);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createPersonalDetail($request)
    {
        try {
            return $this->userPersonalService->createPersonalDetail($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addExperience($request)
    {
        try {
            return $this->userExperienceService->addExperience($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteExperience($id)
    {
        try {
            return $this->userExperienceService->deleteExperience($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addEducation($request)
    {
        try {
            return $this->userEducationService->addEducation($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fileUpload($request){
        try {
            return $this->userExperienceService->fileUpload($request);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function deleteEducation($id)
    {
        try {
            return $this->userEducationService->deleteEducation($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addPatent($request)
    {
        try {
            return $this->userPatentsService->addPatent($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteUserPatent($id)
    {
        try {
            return $this->userPatentsService->deleteUserPatent($id);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function addSkills($request)
    {
        try {
            return $this->userSkillsService->addSkills($request);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function deleteSkill($id)
    {
        try {
            return $this->userSkillsService->deleteSkill($id);
        } catch (\Exception $e) {
            return false;
        }
    }
    public function addCertificate($request)
    {
        try {
            return $this->userCertificatesService->addCertificate($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteUserCertificate($id)
    {
        try {
            return $this->userCertificatesService->deleteUserCertificate($id);
        } catch (\Exception $e) {
            return false;
        }
    }
}
