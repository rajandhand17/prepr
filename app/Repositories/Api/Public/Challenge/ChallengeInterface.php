<?php

namespace App\Repositories\Api\Public\Challenge;

interface ChallengeInterface
{
    public function getList($request);

    public function getChallengeBasedOnSlug($slug);

    public function captureSocialActivity($id, $column, $value);

    public function checkSocialActivity($challenge_id, $column, $action);
}
