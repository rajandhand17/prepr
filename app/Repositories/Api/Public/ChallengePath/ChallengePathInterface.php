<?php

namespace App\Repositories\Api\Public\ChallengePath;

interface ChallengePathInterface
{
    public function getList($request);

    public function getChallengePathBasedOnSlug($slug);

    public function captureSocialActivity($id, $column, $value);

    public function checkSocialActivity($lab_id, $column, $action);
}
