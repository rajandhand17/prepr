<?php

namespace App\Repositories\Api\Public\Challenge;

use App\Models\Challenge;

interface ChallengeInterface
{
    public function getList($request);

    public function getProjectChallenges($request);

    public function getChallengeBasedOnSlug($slug);

    public function getChallengeBasedOnUUID($uuid);

    public function getColumnNameValue($action);

    public function checkSocialActivity($challenge_id, $column, $action);

    public function captureSocialActivity($challenge_id, $column, $value);

    public function getProjectChallengeRequirement($challengeData);

    public function fetchProjectIdsBasedOnChallenge($challengeId);

    public function fetchProjectIds($projectIds, $request);

    public function incrementView(Challenge $challenge);
}
