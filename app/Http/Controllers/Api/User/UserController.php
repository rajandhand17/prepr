<?php

namespace App\Http\Controllers\Api\User;

use App\Repositories\Api\User\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;

class UserController extends AppBaseController
{   
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function userList(Request $request){
        try {
        $userListing=$this->userRepository->getUsers($request);
        if($userListing->count() > 0){
             return $this->sendResponse($userListing,__('responses.user_list_found_success'));
        }
        return $this->sendError(__('labels.labels_mm_nuf'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
