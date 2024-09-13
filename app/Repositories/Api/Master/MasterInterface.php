<?php

namespace App\Repositories\Api\Master;

interface MasterInterface
{
    public function getCategories($request);

    public function getSkills($request);

    public function getTags($request);

    public function getProjectIndustries($request);

    public function getProjectTypes($request);

    public function getStages($request);

    public function getVerticals($request);

    public function getStatus($request);

    public function getSocialLinks($request);

    public function getSkillGroups($request);

    public function getSkillStacks($request);

    public function getRanks($request);

    public function getProjectSubmissionRequirements($request);

    public function getAchievementConditionLists($request);

    public function getHosts($request);

    public function getFlexibleDateDurations($request);

    public function getPitchTemplates($request);

    public function getLabConditions($request);

    public function getSocialConnect($request);

    public function getDurations($request);

    public function getLevels($request);

    public function getPitchTaskData($request);

    public function checkSponsor($request);

    public function uploadSponsorMedia($image);

    public function createSponsor($request, $upload_sponsor_image);

    public function getChallengeAnnouncementRecipient($request);

    public function getTagGroups($request);

    public function getCountries($request);

    public function getJobTitles($request);

    public function getBusinessChallengeTackling($request);
}
