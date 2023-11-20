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

    public function userDetails($profileService)
    {
        try {
            return $this->profileService->userDetails($profileService);
        } catch (\Exception $e) {
            return false;
        }
    }
}
