<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Profile\AddCertificateRequest;
use App\Http\Requests\Profile\AddEducationRequest;
use App\Http\Requests\Profile\AddExperienceRequest;
use App\Http\Requests\Profile\AddPatentRequest;
use App\Http\Requests\Profile\AddPersonalDetailRequest;
use App\Http\Requests\Profile\AddSkillsRequest;
use App\Http\Resources\Profile\AddCertificateResource;
use App\Http\Resources\Profile\AddEducationResource;
use App\Http\Resources\Profile\AddExperienceResource;
use App\Http\Resources\Profile\AddPersonalDetailResource;
use App\Http\Resources\Profile\AddSkillsResource;
use App\Http\Resources\Profile\ProfileResource;
use App\Repositories\Api\Profile\ProfileRepository;

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
                return $this->sendResponse(AddExperienceResource::make($getExperience), __('response.user_experience_created'));
            }

            return $this->sendError(__('responses.user_experience_failed'), 404);
        } catch(\Exception $e) {
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

    public function addPatent(AddPatentRequest $request)
    {
        try {
            $addPatient = $this->profileRepository->addPatent($request);
            if ($addPatient) {
                return $this->sendResponse(AddPersonalDetailResource::make($addPatient), __('responses.user_patent_created'));
            }

            return $this->sendError(__('responses.user_patent_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addSkills(AddSkillsRequest $request)
    {
        try {
            $addSkills = $this->profileRepository->addSkills($request);
            if ($addSkills) {
                return $this->sendResponse(AddSkillsResource::make($addSkills), __('responses.add_skills_create'));
            }

            return $this->sendError(__('responses.add_skills_failed'), 404);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addCertificate(AddCertificateRequest $request){
        try {
            $addCertificate = $this->profileRepository->addCertificate($request);
            if ($addCertificate){
                return $this->sendResponse(AddCertificateResource::make($addCertificate),__('responses.add_certificate_created'));
            }
            return $this->sendError(__('responses.add_certificate_failed'), 404);
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteSkill($id){
        try {
            $deleteSkill=$this->profileRepository->deleteSkill($id);
            if($deleteSkill){
                return $this->sendResponse(null,__('responses.delete_skills'));
            }
            return $this->sendError(__('responses.failed_delete_skills'), 404);
        }catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }
}
