<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Repositories\Api\Auth\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{   
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function userList(){
        try {
            $organization = $this->userRepository->getOrganizationList($request->language);
            if ($organization !== false) {
                return $this->sendResponse(OrganizationResource::collection($organization), __('responses.organization_view_get'));
            }

            return $this->sendError(__('responses.organization_view_get_failed'), 400);
        } catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
