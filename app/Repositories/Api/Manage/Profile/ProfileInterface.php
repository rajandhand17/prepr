<?php

namespace App\Repositories\Api\Manage\Profile;

interface ProfileInterface
{
    public function getProfileBasedOnUserId($user_name);
}
