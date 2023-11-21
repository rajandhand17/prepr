<?php

namespace App\Repositories\Api\Manage\Profile;

interface ProfileInterface
{
    public function getProfileBasedOnUserName($user_name);
}
