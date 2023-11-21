<?php

namespace App\Repositories\Api\Manage\Profile;

use App\Services\Manage\ProfileService;

class ProfileRepository implements ProfileInterface
{
    private $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function getProfileBasedOnUserName($user_name)
    {
        try {
            return $this->profileService->getProfileBasedOnUserName($user_name);
        } catch (\Exception $e) {
            return false;
        }
    }
}
