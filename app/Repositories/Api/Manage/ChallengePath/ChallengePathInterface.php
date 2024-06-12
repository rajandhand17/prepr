<?php

namespace App\Repositories\Api\Manage\ChallengePath;

interface ChallengePathInterface
{
    public function getChallengePathCountBasedOnOrganization($organizationId);

    public function getChallengePathList($request, $organization);

    public function uploadChallengePathMedia($image);

    public function uploadAchievementImage($achievementImage);

    public function createChallengePath($upload_cover_image, $upload_achievement_image, $request, $organizationId);

    public function updateChallengePath($slug, $request, $upload_cover_image, $upload_achievement_image, $organizationId);

    public function checkSlug($slug);

    public function checkNameExistsOrNot($title);

    public function delete($challengePathId);

    public function getChallengePathListName($request, $organization);
}
