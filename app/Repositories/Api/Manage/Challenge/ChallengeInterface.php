<?php

namespace App\Repositories\Api\Manage\Challenge;

interface ChallengeInterface
{
    public function getChallengeList($request, $organization);

    public function uploadChallengeCoverImage($image);

    public function uploadChallengeAssessment($attachment);

    public function uploadChallengeParticipationAchievementImage($image);

    public function createChallenge($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment);

    public function checkSlug($slug);

    public function checkNameExistsOrNot($title);

    public function createChallengeSponsor($request, $challenge);

    public function createChallengeSkillsGroupsStack($request, $challenge);

    public function createChallengeTagsGroups($request, $challenge);

    public function createChallengeRequirement($request, $challenge);

    public function createChallengeAssessmentCriteria($request, $challenge);

    public function createChallengeAssessment($request, $challenge);

    public function createChallengeProjectTemplate($request, $challenge);

    public function updateChallenge($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment);

    public function getChallengeBasedOnSlug($slug);

    public function deleteChallenge($lab_id, $request);

    public function getChallengeAssessmentData($challengeAssessment);
}
