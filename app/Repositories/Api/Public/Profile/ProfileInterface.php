<?php

namespace App\Repositories\Api\Public\Profile;

interface ProfileInterface
{
    public function getProfileBasedOnUserName($user_name);

    public function addPersonalDetail($request);

    public function addPatent($request);

    public function addEducation($request);

    public function addUserExperience($request);
}
