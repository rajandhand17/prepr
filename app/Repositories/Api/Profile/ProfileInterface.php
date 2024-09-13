<?php

namespace App\Repositories\Api\Profile;

interface ProfileInterface
{
    public function getUserByUsername($user_name);

    public function addPersonalDetail($request);

    public function addExperience($request);

    public function deleteExperience($id);

    public function checkUserExperience($id);

    public function addEducation($request);

    public function fileUpload($request);

    public function profileImageUpload($request);

    public function deleteEducation($id);

    public function addPatent($request);

    public function deleteUserPatent($id);

    public function checkUserPatent($id);

    public function addSkills($request);

    public function addTags($request);

    public function deleteProfileTag($id);

    public function deleteProfileSkill($id);

    public function checkUserSkillExists($id);

    public function checkUserTagExists($id);

    public function addCertificate($request);

    public function deleteUserCertificate($id);

    public function checkUserCertificate($id);

    public function checkUserEducation($id);

    public function checkAction($action);

    public function getRecordsBasedOnId($request);

    public function updateFriendsBasedOnAction($request, $column, $value);

    public function friendRequestResponse($request, $value);

    public function followRequestResponse($request, $value);

    public function checkRequests($request);

    public function checkFollowRequests($request);

    public function getFriendsListing($user = null);

    public function getFollowersListing();

    public function getFollowListing();

    public function getFriendRequestList();

    public function getFollowersRequestList();

    public function checkFriendsStatus($request);

    public function removeFriend($request);

    public function unfollowFriend($request, $column);

    public function fileDelete($request);
}
