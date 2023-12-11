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

    public function getCountriesList($language, $search)
    {
        try {
            return $this->profileService->getCountriesList($language, $search);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getInstitutionsList($language, $search)
    {
        try {
            return $this->profileService->getInstitutionsList($language, $search);
        } catch (\Exception $e) {
            return false;
        }
    }
}
