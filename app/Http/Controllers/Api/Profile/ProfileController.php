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
use App\Http\Requests\Profile\ResumeUploadRequest;
use App\Http\Resources\Profile\FriendsResource;
use App\Http\Resources\Profile\ProfileResource;
use App\Http\Resources\Profile\UserCertificateResource;
use App\Http\Resources\Profile\UserEducationResource;
use App\Http\Resources\Profile\UserExperienceResource;
use App\Http\Resources\Profile\UserPatentResource;
use App\Http\Resources\Profile\UserPersonalFilesResource;
use App\Http\Resources\Profile\UserSkillsResource;
use App\Http\Resources\Profile\UserTagsResource;
use App\Http\Resources\User\UserResource;
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
            $userDetails = $this->profileRepository->getUserByUsername($user_name);
            if (!$userDetails) {
            return $this->sendError(__('responses.not_found_user_profile_detail'), 400);
            }
            if ($userDetails->userSetting) {
              $profilePrivacy = $userDetails->userSetting->profile_privacy;
              $projectPrivacy = $userDetails->userSetting->project_privacy;
              if (($profilePrivacy == '1' || $projectPrivacy == '1') && $userDetails->id !== auth()->user()->id) {
              return $this->sendError(__('responses.not_visible_for_others'));
              }
            }
            return $this->sendResponse(ProfileResource::make($userDetails), __('responses.found_user_profile_detail'));
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addPersonalDetail(AddPersonalDetailRequest $request)
    {
        try {
            $createProfile = $this->profileRepository->addPersonalDetail($request);
            if ($createProfile) {
                return $this->sendResponse(ProfileResource::make($createProfile), __('responses.add_user_personal_created'));
            }

            return $this->sendError(__('responses.add_user_personal_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addExperience(AddExperienceRequest $request)
    {
        try {
            $addExperience = $this->profileRepository->addExperience($request);
            if ($addExperience) {
                return $this->sendResponse(UserExperienceResource::collection($addExperience), __('responses.add_user_experience_update'));
            }

            return $this->sendError(__('responses.add_user_experience_failed'), 400);
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

            return $this->sendError(__('responses.failed_delete_experience'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addEducation(AddEducationRequest $request)
    {
        try {
            $addEducation = $this->profileRepository->addEducation($request);
            if ($addEducation) {
                return $this->sendResponse(UserEducationResource::collection($addEducation), __('responses.add_user_education_created'));
            }

            return $this->sendError(__('responses.add_user_education_failed'), 400);
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

            return $this->sendError(__('responses.delete_education_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function addPatent(AddPatentRequest $request)
    {
        try {
            $addPatient = $this->profileRepository->addPatent($request);
            if ($addPatient) {
                return $this->sendResponse(UserPatentResource::collection($addPatient), __('responses.add_user_patent_created'));
            }

            return $this->sendError(__('responses.add_user_patent_failed'), 400);
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

            return $this->sendError(__('responses.add_skills_failed'), 400);
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

            return $this->sendError(__('responses.add_certificate_failed'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteProfileSkill($id)
    {
        try {
            $checkUserDeleteExists = $this->profileRepository->checkUserSkillExists($id);
            if (!$checkUserDeleteExists) {
                return $this->sendError(__('responses.skills_not_found'), 404);
            }
            $deleteSkill = $this->profileRepository->deleteProfileSkill($id);
            if ($deleteSkill) {
                return $this->sendResponse(null, __('responses.delete_skills'));
            }

            return $this->sendError(__('responses.failed_delete_skills'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function deleteProfileTag($id)
    {
        try {
            $checkUserDeleteExists = $this->profileRepository->checkUserTagExists($id);
            if (!$checkUserDeleteExists) {
                return $this->sendError(__('responses.tags_not_found'), 404);
            }
            $deleteTag = $this->profileRepository->deleteProfileTag($id);
            if ($deleteTag) {
                return $this->sendResponse(null, __('responses.delete_tags'));
            }

            return $this->sendError(__('responses.failed_delete_tags'), 400);
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

            return $this->sendError(__('responses.upload_file_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function resumeUpload(ResumeUploadRequest $request)
    {
        try {
            $resumeFile = $this->profileRepository->resumeUpload($request);
            if ($resumeFile) {
                return $this->sendResponse(UserResource::make($resumeFile), __('responses.successfully_upload_file'));
            }

            return $this->sendError(__('responses.upload_file_failed'), 400);
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

            return $this->sendError(__('responses.failed_profile_image'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function friendRequestActivity(FriendRequest $request, $action)
    {
        try {
            if ($request->user_id == auth()->user()->id) {
                return $this->sendError(__('responses.self_request'), 400);
            }
            $activity = $this->profileRepository->checkAction($action);
            if (!$activity) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            $getFriendsRecords = $this->profileRepository->getRecordsBasedOnId($request);

            switch ($action) {
                case 'send':
                    if ($getFriendsRecords !== null && $getFriendsRecords->status == '0') {
                        return $this->sendError(__('responses.user_request_already_send'), 400);
                    }
                    if ($getFriendsRecords !== null && $getFriendsRecords->status == '1') {
                        return $this->sendError(__('responses.user_request_already_accepted'), 400);
                    }
                    $column = 'status';
                    $value = '0';
                    $updateFriendRequest = $this->profileRepository->updateFriendsBasedOnAction($request, $column, $value);
                    if ($updateFriendRequest) {
                        return $this->sendResponse(null, __('responses.success_'.$action.'_message'), 200);
                    }
                    break;
                case 'follow':
                    if ($getFriendsRecords == null || $getFriendsRecords->status !== '1') {
                        return $this->sendError(__('responses.not_friend'), 404);
                    }
                    $column = $getFriendsRecords->user_id == $request->user_id ? 'user_follow' : 'reference_follow';
                    $value = '2';
                    if ($getFriendsRecords->$column == '2') {
                        return $this->sendError(__('responses.already_follow_friend_request'), 400);
                    }
                    $updateFriendRequest = $this->profileRepository->updateFriendsBasedOnAction($request, $column, $value);
                    if ($updateFriendRequest) {
                        return $this->sendResponse(null, __('responses.success_'.$action.'_message'), 200);
                    }
                    break;
                case in_array($action, ['accept', 'reject']):
                    $checkRequestExists = $this->profileRepository->checkRequests($request);
                    if ($checkRequestExists == null) {
                        return $this->sendError(__('responses.friends_requesting_listing_no_records'), 404);
                    }
                    $value = $activity;
                    if ($value !== null && $getFriendsRecords) {
                        $updateResponse = $this->profileRepository->friendRequestResponse($request, $value);
                        if ($updateResponse) {
                            return $this->sendResponse(null, __('responses.'.$action.'_friend_request'));
                        }
                    }
                    break;
                case 'un-follow':
                    if ($getFriendsRecords == null) {
                        return $this->sendError(__('responses.not_friend'), 404);
                    }
                    $column = $getFriendsRecords->user_id == $request->user_id ? 'user_follow' : 'reference_follow';
                    if ($getFriendsRecords->$column !== '2') {
                        return $this->sendError(__('responses.not_follow_status'), 400);
                    }
                    $response = $this->profileRepository->unfollowFriend($request, $column);
                    if ($response) {
                        return $this->sendResponse($response, __('responses.unfollow_friend_successfully'));
                    }
                    break;
                case 'un-friend':
                    $checkFriends = $this->profileRepository->checkFriendsStatus($request);
                    if ($checkFriends == null) {
                        return $this->sendError(__('responses.not_friend_status'), 400);
                    }
                    $response = $this->profileRepository->removeFriend($request);
                    if ($response) {
                        return $this->sendResponse($response, __('responses.remove_friend_successfully'));
                    }
                    break;
                default:

                    return $this->sendError(__('responses.handler_bad_request'), 400);
            }

            return $this->sendError(__('responses.send_error'), 400);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getFriendListingBasedOnActivity($username, $activity = null)
    {
        try {
            if (!in_array($activity, ['follow', 'pending', 'followers', null])) {
                return $this->sendError(__('responses.handler_bad_request'), 400);
            }
            switch ($activity) {
                case 'pending':
                    $friendsListing = $this->profileRepository->getFriendRequestList();
                    break;
                case 'followers':
                    $friendsListing = $this->profileRepository->getFollowersListing();
                    break;
                case 'follow':
                    $friendsListing = $this->profileRepository->getFollowListing();
                    break;
                default:
                    $friendsListing = $this->profileRepository->getFriendsListing();
                    break;
            }
            if ($friendsListing) {
                return $this->sendResponse(FriendsResource::collection($friendsListing), __('responses.friends_listing'));
            }

            return $this->sendResponse([], __('responses.friends_listing'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
