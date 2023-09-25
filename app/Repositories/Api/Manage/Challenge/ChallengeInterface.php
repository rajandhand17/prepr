<?php

namespace App\Repositories\Api\Manage\Challenge;

interface ChallengeInterface
{
    public function uploadChallengeCoverImage($image);

    public function uploadChallengeParticipationAchievementImage($image);

    public function createChallenge($request, $upload_cover_image, $upload_achievement_image);

    public function createChallengeSponsor($request, $challenge);

    public function createChallengeSkillsGroupsStack($request, $challenge);

    public function createChallengeTagsGroups($request, $challenge);

    public function createChallengeRequirement($request, $challenge);

    public function createChallengeAssessmentCriteria($request, $challenge);

    public function createChallengeAssessment($request, $challenge);

    public function createChallengeProjectTemplate($request, $challenge);
}
