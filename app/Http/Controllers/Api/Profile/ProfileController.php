<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Profile\AddCertificateRequest;
use App\Http\Requests\Profile\AddEducationRequest;
use App\Http\Requests\Profile\AddExperienceRequest;
use App\Http\Requests\Profile\AddPatentRequest;
use App\Http\Requests\Profile\AddPersonalDetailRequest;
use App\Http\Requests\Profile\AddSkillsRequest;
use App\Http\Resources\Profile\AddPersonalDetailResource;
use App\Http\Resources\Profile\ProfileResource;
use App\Repositories\Api\Profile\ProfileRepository;
use App\Services\ProfileService;

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

    public function addUserExperience(AddExperienceRequest $request)
    {
        try {
            $getExperience = $this->profileRepository->addUserExperience($request);
            if ($getExperience) {
                return $this->sendResponse(null, __('responses.user_experience_update'));
            }

            return $this->sendError(__('responses.user_experience_failed'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteUserExperience($id)
    {
        try {
            $checkUserExperienceExistsOrNot = ProfileService::checkUserExperience($id);
            if (!$checkUserExperienceExistsOrNot) {
                return $this->sendError(__('responses.user_experience_not_exists'), 404);
            }
            $getUserExperience = $this->profileRepository->deleteUserExperience($id);
            if ($getUserExperience) {
                return $this->sendResponse(null, __('responses.delete_experience'));
            }

            return $this->sendError(__('responses.failed_delete_experience'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addEducation(AddEducationRequest $request)
    {
        try {
            $addEducation = $this->profileRepository->addEducation($request);
            if ($addEducation) {
                return $this->sendResponse(null, __('responses.user_education_created'));
            }

            return $this->sendError(__('responses.user_education_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteEducation($id)
    {
        try {
            $checkEducationExistsOrNot = ProfileService::checkUserEducation($id);
            if (!$checkEducationExistsOrNot) {
                return $this->sendError(__('responses.user_education_not_found'), 404);
            }
            $deleteEducation = $this->profileRepository->deleteEducation($id);
            if ($deleteEducation) {
                return $this->sendResponse(null, __('responses.delete_education_success'));
            }

            return $this->sendError(__('responses.delete_education_failed'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addPatent(AddPatentRequest $request)
    {
        try {
            $addPatient = $this->profileRepository->addPatent($request);
            if ($addPatient) {
                return $this->sendResponse(null, __('responses.user_patent_created'));
            }

            return $this->sendError(__('responses.user_patent_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deletePatent($id)
    {
        try {
            $checkUserPatentExists = ProfileService::checkUserPatent($id);

            if (!$checkUserPatentExists) {
                return $this->sendError(__('responses.user_patient_not_found'), 404);
            }
            $deleteUserPatent = $this->profileRepository->deleteUserPatent($id);
            if ($deleteUserPatent) {
                return $this->sendResponse(null, __('responses.user_patient_deleted'), 200);
            }

            return $this->sendError(__('responses.user_patient_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addSkills(AddSkillsRequest $request)
    {
        try {
            $addSkills = $this->profileRepository->addSkills($request);
            if ($addSkills) {
                return $this->sendResponse(null, __('responses.add_skills_create'));
            }

            return $this->sendError(__('responses.add_skills_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addCertificate(AddCertificateRequest $request)
    {
        try {
            $addCertificate = $this->profileRepository->addCertificate($request);
            if ($addCertificate) {
                return $this->sendResponse(null, __('responses.add_certificate_created'));
            }

            return $this->sendError(__('responses.add_certificate_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteSkill($id)
    {
        try {
            $checkUserDeleteExists = ProfileService::checkUserSkillDeleteExists($id);
            if (!$checkUserDeleteExists) {
                return $this->sendError(__('responses.skills_not_found'), 404);
            }
            $deleteSkill = $this->profileRepository->deleteSkill($id);
            if ($deleteSkill) {
                return $this->sendResponse(null, __('responses.delete_skills'));
            }

            return $this->sendError(__('responses.failed_delete_skills'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteCertificate($id)
    {
        try {
            $checkExistsCertificateOrNot = ProfileService::checkUserCertificate($id);
            if (!$checkExistsCertificateOrNot) {
                return $this->sendError(__('responses.user_certificate_not_found'), 404);
            }
            $deleteCertificate = $this->profileRepository->deleteUserCertificate($id);
            if ($deleteCertificate) {
                return $this->sendResponse(null, __('responses.user_certificate_deleted'));
            }
            return $this->sendError(__('responses.user_certificate_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
