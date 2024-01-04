<?php

namespace App\Repositories\Api\Profile;

use App\Services\FriendService;
use App\Services\UserAddressService;
use App\Services\UserCertificateService;
use App\Services\UserEducationService;
use App\Services\UserExperienceService;
use App\Services\UserPatentService;
use App\Services\UserPersonalService;
use App\Services\UserService;
use App\Services\UserSkillsService;
use DB;

class ProfileRepository implements ProfileInterface
{
    private $userService;

    private $userPersonalService;

    private $userExperienceService;

    private $userEducationService;

    private $userSkillsService;

    private $userPatentsService;

    private $userCertificatesService;

    private $userAddressService;

    private $friendService;

    public function __construct(FriendService $friendService, UserAddressService $userAddressService, UserCertificateService $userCertificatesService, UserPatentService $userPatentsService, UserSkillsService $userSkillsService, UserService $userService, UserPersonalService $userPersonalService, UserExperienceService $userExperienceService, UserEducationService $userEducationService)
    {
        $this->userService = $userService;
        $this->userPersonalService = $userPersonalService;
        $this->userExperienceService = $userExperienceService;
        $this->userEducationService = $userEducationService;
        $this->userSkillsService = $userSkillsService;
        $this->userPatentsService = $userPatentsService;
        $this->userCertificatesService = $userCertificatesService;
        $this->userAddressService = $userAddressService;
        $this->friendService = $friendService;
    }

    public function getUserByUsername($user_name)
    {
        try {
            return $this->userService->getUserByUsername($user_name);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addPersonalDetail($request)
    {
        try {
            $personalDetail = DB::transaction(function () use ($request) {
                $createUser = $this->userService->addUserName($request);
                $createPersonalDetail = $this->userPersonalService->addPersonalDetail($request);
                $createAddress = $this->userAddressService->addUserAddress($request);

                return [
                    'createdUser'             => $createUser,
                    'createdPersonalDetail'   => $createPersonalDetail,
                    'createdAddress'          => $createAddress,
                ];
            });
            if ($personalDetail['createdUser'] && $personalDetail['createdPersonalDetail'] && $personalDetail['createdAddress']) {
                DB::commit();

                return $personalDetail['createdPersonalDetail'];
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            DB::rollBack();

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

    public function checkUserExperience($id)
    {
        try {
            return $this->userExperienceService->checkUserExperience($id);
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

    public function fileUpload($request)
    {
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

    public function checkUserPatent($id)
    {
        try {
            return $this->userPatentsService->checkUserPatent($id);
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

    public function checkUserSkillExists($id)
    {
        try {
            return $this->userSkillsService->checkUserSkillExists($id);
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

    public function checkUserCertificate($id)
    {
        try {
            return $this->userCertificatesService->checkUserCertificate($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkUserEducation($id)
    {
        try {
            return $this->userEducationService->checkUserEducation($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendFriendRequest($request)
    {
        try {
            return $this->friendService->sendFriendRequest($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendRequest($request)
    {
        try {
            return $this->friendService->checkFriendRequest($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendRequestBasedOnAction($request,$column,$value){
        try {
            return $this->friendService->checkFriendRequestBasedOnAction($request,$column,$value);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function friendRequest($request,$column,$value)
    {
        try {
            return $this->friendService->friendRequest($request, $column,$value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getActionValue($action)
    {
        try {
            return $this->friendService->getActionValue($action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFriendsListing($getColumnName){
        try {
            return $this->friendService->getFriendsListing($getColumnName);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function getFriendRequestList($column){
        try {
            return $this->friendService->getFriendRequestList($column);
        }catch (\Exception $e) {
            return false;
        }
    }
    public function getColumnName($column){
        try {
            return $this->friendService->getColumnName($column);
        }catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendsStatus($request){
        try {
            return $this->friendService->checkFriendsStatus($request);
        }catch (\Exception $e) {
            return false;
        }
    }
    public function removeFriend($request){
        try {
            return $this->friendService->removeFriend($request);
        }catch (\Exception $e) {
            return false;
        }
    }
}
