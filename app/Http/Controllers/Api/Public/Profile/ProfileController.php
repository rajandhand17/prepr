<?php

namespace App\Http\Controllers\Api\Public\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\AddCountryListResource;
use App\Repositories\Api\Public\Profile\ProfileRepository;
use Illuminate\Http\Request;

class ProfileController extends AppBaseController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }
}
