<?php

namespace App\Http\Controllers\Api\Manage\Profile;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Manage\ResourceCollection\ResourceCollectionResource;
use App\Repositories\Api\Manage\Profile\ProfileRepository;

class ProfileController extends AppBaseController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository){
        $this->profileRepository = $profileRepository;
    }

    public function userDetails($userName){
        try {
            $response = $this->profileRepository->userDetails($userName);
            if ($response) {
                return $this->sendResponse(ResourceCollectionResource::make($response), __('responses.found_resource_collection_list'));
            }
            return $this->sendError(__('responses.not_found_resource_collection_view'), 404);
        }catch(\Exception $e){
            return false;
        }
    }
}
