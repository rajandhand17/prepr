<?php

namespace App\Http\Controllers\Api\Manage\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Manage\Profile\AddEducationRequest;
use App\Http\Requests\Manage\Profile\AddExperienceRequest;
use App\Http\Requests\Manage\Profile\AddPatentRequest;
use App\Http\Requests\Manage\Profile\AddPersonalDetailRequest;
use App\Http\Resources\Manage\Profile\AddEducationResource;
use App\Http\Resources\Manage\Profile\AddExperienceResource;
use App\Http\Resources\Manage\Profile\AddPersonalDetailResource;
use App\Http\Resources\Manage\Profile\ProfileResource;
use App\Repositories\Api\Manage\Profile\ProfileRepository;

class ProfileController extends AppBaseController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }
}
