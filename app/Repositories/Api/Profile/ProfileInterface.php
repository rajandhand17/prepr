<?php

namespace App\Repositories\Api\Profile;

interface ProfileInterface
{
    public function getProfileBasedOnUserName($user_name);

    public function addPersonalDetail($request);

    public function addPatient($request);

    public function addEducation($request);

    public function addUserExperience($request);
}
