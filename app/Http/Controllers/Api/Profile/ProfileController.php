<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Profile\AddCertificateRequest;
use App\Http\Requests\Profile\AddEducationRequest;
use App\Http\Requests\Profile\AddExperienceRequest;
use App\Http\Requests\Profile\AddPatentRequest;
use App\Http\Requests\Profile\AddSkillsRequest;
use App\Http\Requests\Profile\FileUploadRequest;
use App\Http\Requests\Profile\PersonalDetailRequest;
use App\Http\Resources\Profile\ProfileResource;
use App\Http\Resources\Profile\UserCertificateResource;
use App\Http\Resources\Profile\UserEducationResource;
use App\Http\Resources\Profile\UserExperienceResource;
use App\Http\Resources\Profile\UserPatentResource;
use App\Http\Resources\Profile\UserPersonalFilesResource;
use App\Http\Resources\Profile\UserSkillsResource;
use App\Repositories\Api\Profile\ProfileRepository;
use App\Services\UserCertificateService;
use App\Services\UserEducationService;
use App\Services\UserExperienceService;
use App\Services\UserPatentService;
use App\Services\UserSkillsService;

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
            $getUserDetails = $this->profileRepository->getUserByUsername($user_name);
            if ($getUserDetails) {
                return $this->sendResponse(ProfileResource::make($getUserDetails), __('responses.found_user_profile_detail'));
            }

            return $this->sendError(__('responses.not_found_user_profile_detail'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function create(PersonalDetailRequest $request)
    {
        try {
            $createProfile = $this->profileRepository->createPersonalDetail($request);
            if ($createProfile) {
                return $this->sendResponse(ProfileResource::make($createProfile), __('responses.user_personal_created'));
            }

            return $this->sendError(__('responses.user_personal_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addExperience(AddExperienceRequest $request)
    {
        try {
            $addExperience = $this->profileRepository->addExperience($request);
            if ($addExperience) {
                return $this->sendResponse(UserExperienceResource::collection($addExperience), __('responses.user_experience_update'));
            }

            return $this->sendError(__('responses.user_experience_failed'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteExperience($id)
    {
        try {
            $checkUserExperienceExistsOrNot = UserExperienceService::checkUserExperience($id);
            if (!$checkUserExperienceExistsOrNot) {
                return $this->sendError(__('responses.user_experience_not_exists'), 404);
            }
            $getUserExperience = $this->profileRepository->deleteExperience($id);
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
                return $this->sendResponse(UserEducationResource::collection($addEducation), __('responses.user_education_created'));
            }

            return $this->sendError(__('responses.user_education_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteEducation($id)
    {
        try {
            $checkEducationExistsOrNot = UserEducationService::checkUserEducation($id);
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
                return $this->sendResponse(UserPatentResource::collection($addPatient), __('responses.user_patent_created'));
            }

            return $this->sendError(__('responses.user_patent_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deletePatent($id)
    {
        try {
            $checkUserPatentExists = UserPatentService::checkUserPatent($id);
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
                return $this->sendResponse(UserSkillsResource::collection($addSkills), __('responses.add_skills_create'));
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
                return $this->sendResponse(UserCertificateResource::collection($addCertificate), __('responses.add_certificate_created'));
            }

            return $this->sendError(__('responses.add_certificate_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteSkill($id)
    {
        try {
            $checkUserDeleteExists = UserSkillsService::checkUserSkillDeleteExists($id);
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
            $checkExistsCertificateOrNot = UserCertificateService::checkUserCertificate($id);
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

    public function fileUpload(FileUploadRequest $request)
    {
        try {
            $uploadFile = $this->profileRepository->fileUpload($request);
            if ($uploadFile) {
                return $this->sendResponse(UserPersonalFilesResource::make($uploadFile), __('responses.successfully_upload_file'));
            }

            return $this->sendError(__('responses.user_experience_failed'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
