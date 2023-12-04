<?php

namespace App\Repositories\Api\Manage\Profile;

interface ProfileInterface
{
    public function getProfileBasedOnUserName($user_name);

    public function addPersonalDetail($request);

    public function addEducation($request);

}
