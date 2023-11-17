<?php

namespace App\Repositories\Api\Manage\Profile;

use App\Services\Manage\ProfileService;
use DB;

class ProfileRepository implements ProfileInterface
{

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }
}
