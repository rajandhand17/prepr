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
use App\Services\UserTagsService;
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

    private $userTagsService;

    public function __construct(FriendService $friendService, UserAddressService $userAddressService, UserCertificateService $userCertificatesService, UserPatentService $userPatentsService, UserTagsService $userTagsService, UserSkillsService $userSkillsService, UserService $userService, UserPersonalService $userPersonalService, UserExperienceService $userExperienceService, UserEducationService $userEducationService)
    {
        $this->userService = $userService;
        $this->userPersonalService = $userPersonalService;
        $this->userExperienceService = $userExperienceService;
        $this->userEducationService = $userEducationService;
        $this->userSkillsService = $userSkillsService;
        $this->userTagsService = $userTagsService;
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
                $updateUser = $this->userService->addUserName($request);
                $updatePersonalDetail = $this->userPersonalService->addPersonalDetail($request);
                $updateAddress = $this->userAddressService->addUserAddress($request);

                return [
                    'updateUser'             => $updateUser,
                    'updatePersonalDetail'   => $updatePersonalDetail,
                    'updateAddress'          => $updateAddress,
                ];
            });
            if ($personalDetail['updateUser'] && $personalDetail['updatePersonalDetail'] && $personalDetail['updateAddress']) {
                DB::commit();

                return $personalDetail['updateUser'];
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

    public function profileImageUpload($request)
    {
        try {
            return $this->userPersonalService->profileImageUpload($request);
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
        } catch(\Exception $e) {
            return false;
        }
    }

    public function addTags($request)
    {
        try {
            return $this->userTagsService->addTags($request);
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

    public function deleteTag($id)
    {
        try {
            return $this->userTagsService->deleteTag($id);
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

    public function checkUserTagExists($id)
    {
        try {
            return $this->userTagsService->checkUserTagExists($id);
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

    public function checkAction($action)
    {
        try {
            return $this->friendService->checkAction($action);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getRecordsBasedOnId($request)
    {
        try {
            return $this->friendService->getRecordsBasedOnId($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createFriendsBasedOnAction($request, $column, $value)
    {
        try {
            return $this->friendService->createFriendsBasedOnAction($request, $column, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function responseOfFriendRequest($request, $value)
    {
        try {
            return $this->friendService->responseOfFriendRequest($request, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function responseOfFollowRequest($request, $value)
    {
        try {
            return $this->friendService->responseOfFollowRequest($request, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkRequests($request)
    {
        try {
            return $this->friendService->checkRequests($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFollowRequests($request)
    {
        try {
            return $this->friendService->checkFollowRequests($request);
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

    public function getFriendsListing()
    {
        try {
            return $this->friendService->getFriendsListing();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFollowersListing()
    {
        try {
            return $this->friendService->getFollowersListing();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFriendRequestList()
    {
        try {
            return $this->friendService->getFriendRequestList();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFollowersRequestList()
    {
        try {
            return $this->friendService->getFollowersRequestList();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendsStatus($request)
    {
        try {
            return $this->friendService->checkFriendsStatus($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFollowStatus($request)
    {
        try {
            return $this->friendService->checkFollowStatus($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function removeFriend($request)
    {
        try {
            return $this->friendService->removeFriend($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unfollowFriend($request)
    {
        try {
            return $this->friendService->unfollowFriend($request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
