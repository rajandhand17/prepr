<?php

namespace App\Repositories\Api\Public\Profile;

use App\Services\Public\ProfileService;

class ProfileRepository implements ProfileInterface
{
    private $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

}
