<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserSearchResource;
use App\Repositories\Api\User\UserRepository;
use Illuminate\Http\Request;

class UserController extends AppBaseController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        try {
            $userListing = $this->userRepository->getUsers($request);
            if ($userListing != false) {
                return $this->sendResponse(UserSearchResource::collection($userListing), __('responses.found_user_list'));
            }

            return $this->sendError(__('responses.found_user_list'), 404);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getLoggedinUser()
    {
        try {
            return $this->sendResponse(UserResource::make(auth()->user()), __('responses.found_user_profile_detail'));
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
