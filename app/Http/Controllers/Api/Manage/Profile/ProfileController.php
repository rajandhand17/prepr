<?php

namespace App\Http\Controllers\Api\Manage\Profile;

use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Manage\Profile\ProfileRepository;

class ProfileController extends AppBaseController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository){
        $this->profileRepository = $profileRepository;
    }

    public function userDetails($userDetails){
        $this->profileRepository=$userDetails;
    }
}
