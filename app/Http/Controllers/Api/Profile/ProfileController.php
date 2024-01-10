<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Profile\AddCertificateRequest;
use App\Http\Requests\Profile\AddEducationRequest;
use App\Http\Requests\Profile\AddExperienceRequest;
use App\Http\Requests\Profile\AddPatentRequest;
use App\Http\Requests\Profile\AddPersonalDetailRequest;
use App\Http\Requests\Profile\AddSkillsRequest;
use App\Http\Requests\Profile\AddTagsRequest;
use App\Http\Requests\Profile\FileUploadRequest;
use App\Http\Requests\Profile\FriendRequest;
use App\Http\Requests\Profile\ProfileUploadRequest;
use App\Http\Resources\Profile\FriendsResource;
use App\Http\Resources\Profile\ProfileResource;
use App\Http\Resources\Profile\UserCertificateResource;
use App\Http\Resources\Profile\UserEducationResource;
use App\Http\Resources\Profile\UserExperienceResource;
use App\Http\Resources\Profile\UserPatentResource;
use App\Http\Resources\Profile\UserPersonalFilesResource;
use App\Http\Resources\Profile\UserSkillsResource;
use App\Http\Resources\Profile\UserTagsResource;
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
            $getUserDetails = $this->profileRepository->getUserByUsername($user_name);
            if ($getUserDetails) {
                return $this->sendResponse(ProfileResource::make($getUserDetails), __('responses.found_user_profile_detail'));
            }

            return $this->sendError(__('responses.not_found_user_profile_detail'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addPersonalDetail(AddPersonalDetailRequest $request)
    {
        try {
            $createProfile = $this->profileRepository->addPersonalDetail($request);
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
            $checkUserExperienceExistsOrNot = $this->profileRepository->checkUserExperience($id);
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
            $checkEducationExistsOrNot = $this->profileRepository->checkUserEducation($id);
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
            $checkUserPatentExists = $this->profileRepository->checkUserPatent($id);
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

    public function addTags(AddTagsRequest $request)
    {
        try {
            $addTags = $this->profileRepository->addTags($request);
            if ($addTags) {
                return $this->sendResponse(UserTagsResource::collection($addTags), __('responses.add_tags_create'));
            }

            return $this->sendError(__('responses.add_tags_failed'), 404);
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
            $checkUserDeleteExists = $this->profileRepository->checkUserSkillExists($id);
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

    public function deleteTag($id)
    {
        try {
            $checkUserDeleteExists = $this->profileRepository->checkUserTagExists($id);
            if (!$checkUserDeleteExists) {
                return $this->sendError(__('responses.tags_not_found'), 404);
            }
            $deleteTag = $this->profileRepository->deleteTag($id);
            if ($deleteTag) {
                return $this->sendResponse(null, __('responses.delete_tags'));
            }

            return $this->sendError(__('responses.failed_delete_tags'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteCertificate($id)
    {
        try {
            $checkExistsCertificateOrNot = $this->profileRepository->checkUserCertificate($id);
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

            return $this->sendError(__('responses.upload_file_failed'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function profileImageUpload(ProfileUploadRequest $request)
    {
        try {
            $profile = $this->profileRepository->profileImageUpload($request);
            if ($profile) {
                return $this->sendResponse(ProfileResource::make($profile), __('responses.successfully_profile'));
            }

            return $this->sendError(__('responses.failed_profile_image'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function friendRequest(FriendRequest $request, $action)
    {
        try {
            $getActionValue = $this->profileRepository->getActionValue($action);
            if (!$getActionValue) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            if ($action == 'send' || $action == 'follow') {
                $getFriendsRecords = $this->profileRepository->getRecordsBasedOnId($request);
                $column = $getActionValue['column'];
                if ($getFriendsRecords !== null && $getFriendsRecords->$column == '0') {
                    return $this->sendError(__('responses.user_request_already_send'), 403);
                }
                if ($getFriendsRecords !== null && $getFriendsRecords->$column == '1') {
                    return $this->sendError(__('responses.user_request_already_accepted'), 403);
                }
                $createFriendRequest = $this->profileRepository->createFriendsBasedOnAction($request, $getActionValue['column'], $getActionValue['value']);
                if ($createFriendRequest) {
                    return $this->sendResponse(null, __('responses.success_'.$action.'_message'));
                }

                return $this->sendError(__('responses.failed_'.$action.'_message'), 403);
            }

            return $this->sendError(__('responses.send_error'), 402);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function friendRequestResponse(FriendRequest $request, $activity)
    {
        try {
            $checkRequestExists = $this->profileRepository->checkRequests($request);
            if ($checkRequestExists == null) {
                return $this->sendError(__('responses.friends_requesting_listing_no_records'), 404);
            }
            $value = $this->profileRepository->checkAction($activity);
            if ($value !== null) {
                $getFriendsRecords = $this->profileRepository->getRecordsBasedOnId($request);
                if ($getFriendsRecords) {
                    $updateResponse = $this->profileRepository->responseOfFriendRequest($request, $value);
                    if ($updateResponse) {
                        return $this->sendResponse(null, __('responses.'.$activity.'_friend_request'));
                    }
                }
            }

            return $this->sendError(__('responses.send_error'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function followRequestResponse(FriendRequest $request, $activity)
    {
        try {
            $checkRequestExists = $this->profileRepository->checkFollowRequests($request);
            if ($checkRequestExists == null) {
                return $this->sendError(__('responses.friends_requesting_listing_no_records'), 404);
            }
            $value = $this->profileRepository->checkAction($activity);
            if ($value !== null) {
                $updateResponse = $this->profileRepository->responseOfFollowRequest($request, $value);
                if ($updateResponse) {
                    return $this->sendResponse(null, __('responses.'.$activity.'_follow_friend_request'));
                }
            }

            return $this->sendError(__('responses.send_error'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getFriendsListing()
    {
        try {
            $friendsListing = $this->profileRepository->getFriendsListing();
            if ($friendsListing) {
                return $this->sendResponse(FriendsResource::collection($friendsListing), __('responses.friends_listing'));
            }

            return $this->sendError(__('responses.friends_listing'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getFollowersListing()
    {
        try {
            $getFollowersListing = $this->profileRepository->getFollowersListing();
            if ($getFollowersListing) {
                return $this->sendResponse(FriendsResource::collection($getFollowersListing), __('responses.friends_listing'));
            }

            return $this->sendError(__('responses.friends_listing'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getFriendRequestList()
    {
        try {
            $getFriendRequestList = $this->profileRepository->getFriendRequestList();
            if (!empty($getFriendRequestList)) {
                return $this->sendResponse(FriendsResource::collection($getFriendRequestList), __('responses.friends_request_listing'));
            }

            return $this->sendError(__('responses.friends_requesting_listing_no_records'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getFollowersRequestList()
    {
        try {
            $getFollowersRequestList = $this->profileRepository->getFollowersRequestList();
            if (!empty($getFollowersRequestList)) {
                return $this->sendResponse(FriendsResource::collection($getFollowersRequestList), __('responses.friends_request_listing'));
            }

            return $this->sendError(__('responses.friends_requesting_listing_no_records'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function unFriend(FriendRequest $request)
    {
        try {
            $checkFriends = $this->profileRepository->checkFriendsStatus($request);
            if ($checkFriends == null) {
                return $this->sendError(__('responses.not_friend_status'), 406);
            }
            $response = $this->profileRepository->removeFriend($request);
            if ($response) {
                return $this->sendResponse($response, __('responses.remove_friend_successfully'));
            }

            return $this->sendError(__('responses.remove_friend_failed'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function unFollow(FriendRequest $request)
    {
        try {
            $checkFollowStatus = $this->profileRepository->checkFollowStatus($request);
            if ($checkFollowStatus == null) {
                return $this->sendError(__('responses.not_follow_status'), 402);
            }
            $response = $this->profileRepository->unfollowFriend($request);
            if ($response) {
                return $this->sendResponse($response, __('responses.remove_friend_successfully'));
            }

            return $this->sendError(__('responses.remove_friend_failed'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
