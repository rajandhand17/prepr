<?php

namespace App\Repositories\Api\Profile;

use App\Helpers\MixpanelHelper;
use App\Helpers\ResumeParserHelper;
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
                $profile_data = [
                    'type' => 'certificate',
                    'info' => $request->all()
                ];
                MixpanelHelper::mixpanel_tracking(config('mixpanel.update_profile'), $profile_data, auth()->user(), $request->ip());
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

    public function resumeUpload($request)
    {
        try {
            $getResumeData = ResumeParserHelper::getResumeData($request);
            $user = auth()->user();
            if (!empty($getResumeData)) {
                if ($getResumeData['data']) {
                    $getResume = DB::transaction(function () use ($getResumeData, $user) {
                        $userSKills = $this->userSkillsService->addUserSkillsByUsingResumeData($getResumeData);
                        $userExperience = $this->userExperienceService->addUserExperienceByUsingResumeData($getResumeData, $user);

                        return [
                            'skills'     => $userSKills,
                            'experience' => $userExperience,
                        ];
                    });
                }
                if ($getResume['skills'] && $getResume['experience']) {
                    DB::commit();

                    return $user;
                }
                DB::rollBack();
            }
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

    public function deleteProfileSkill($id)
    {
        try {
            return $this->userSkillsService->deleteProfileSkill($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteProfileTag($id)
    {
        try {
            return $this->userTagsService->deleteProfileTag($id);
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

    public function friendRequestResponse($request, $value)
    {
        try {
            return $this->friendService->friendRequestResponse($request, $value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function followRequestResponse($request, $value)
    {
        try {
            return $this->friendService->followRequestResponse($request, $value);
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

    public function getFollowListing()
    {
        try {
            return $this->friendService->getFollowListing();
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

    public function removeFriend($request)
    {
        try {
            return $this->friendService->removeFriend($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function unfollowFriend($request, $column)
    {
        try {
            return $this->friendService->unfollowFriend($request, $column);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateFriendsBasedOnAction($request, $column, $value)
    {
        try {
            return $this->friendService->updateFriendsBasedOnAction($request, $column, $value);
        } catch (\Exception $e) {
            return false;
        }
    }
}
