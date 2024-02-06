<?php

namespace App\Listeners\Users;

use App\Services\UserAddressService;
use App\Services\UserCertificateService;
use App\Services\UserExperienceService;
use App\Services\UserPatentService;
use App\Services\UserPersonalService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleDeleteUserAssociatedData
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $userId=$this->userId;
        $userAddress=UserAddressService::deleteUserAddressBasedOnUserId($userId);
        if(!$userAddress){
            return false;
        }
        $userCertificate=UserCertificateService::deleteCertificateBasedOnUserId($userId);
        if(!$userCertificate){
            return false;
        }
        $userExperience=UserExperienceService::deleteUserExperienceBasedOnUserId($userId);
        if(!$userExperience){
            return false;
        }
        $userPatent=UserPatentService::deleteUserPatentBasedOnUserId($userId);
        if(!$userPatent){
            return false;
        }

    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        //
    }
}
