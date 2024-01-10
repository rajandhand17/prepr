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

    public function deleteEducation($id);

    public function addPatent($request);

    public function deleteUserPatent($id);

    public function checkUserPatent($id);
    public function addSkills($request);

    public function deleteSkill($id);

    public function checkUserSkillExists($id);

    public function addCertificate($request);

    public function deleteUserCertificate($id);

    public function checkUserCertificate($id);

    public function checkUserEducation($id);

    public function checkAction($action);

    public function getRecordsBasedOnId($request);

    public function createFriendsBasedOnAction($request,$column,$value);

    public function responseOfFriendRequest($request,$value);

    public function responseOfFollowRequest($request,$value);

    public function checkRequests($request);

    public function checkFollowRequests($request);

    public function getActionValue($action);
    public function getFriendsListing();

    public function getFollowersListing();

    public function getFriendRequestList();

    public function getFollowersRequestList();
    public function checkFriendsStatus($request);
    public function checkFollowStatus($request);
    public function removeFriend($request);

    public function unfollowFriend($request);

}
