<?php

namespace App\Repositories\Api\Manage\Challenge;

use App\Models\Challenge;

interface ChallengeInterface
{
    public function getChallengeCountBasedOnOrganization($organizationId);

    public function getChallengeList($request, $organization);

    public function uploadChallengeCoverImage($image);

    public function uploadChallengeAssessment($attachment);

    public function createChallenge($request, $uploaded_cover_image, $uploaded_achievement_image, $uploaded_assessment_attachment, $organizationData);

    public function createChallengeUsingAIPreview($request);

    public function createChallengeFromResourceUsingAIPreview($request);

    public function createChallengeUsingAI($request, $upload_cover_image, $upload_achievement_image, $upload_assessment_attachment, $organization);

    public function uploadChallengeParticipationAchievementImage($image);

    public function createChallengeSponsor($request, $challenge);

    public function createChallengeSkillsGroupsStack($request, $challenge);

    public function createChallengeRequirement($request, $challenge);

    public function createChallengeProjectTemplate($request, $challenge);

    public function updateChallenge($slug, $request, $update_cover_image, $update_participation_achievement_image, $update_assessment_attachment, $organizationData);

    public function getChallengeBasedOnSlug($slug);

    public function deleteChallenge($challenge_id, $request);

    public function checkSlug($slug);

    public function checkNameExistsOrNot($title);

    public function getChallengeAssessmentData($challengeAssessment);

    public function updateChallengeAssessment($challengeId, $update_assessment_attachment, $request);

    public function cloneChallenge($challengeId, $organization);

    public function createChallengeAnnouncement($challengeId, $request);

    public function deleteChallengeAnnouncement($challengeAnnouncementId);

    public function getChallengeListName($request, $organization);

    public function selectChallengeWinner($challengeData, $request);

    public function fetchProjectIdsBasedOnChallenge($challengeId);

    public function fetchProjectIds($projectIds, $request);

    public function incrementView(Challenge $challenge);
}
