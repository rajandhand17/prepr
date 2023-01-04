<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Api\Auth\AuthRepository;
use App\Http\Resources\Auth\RegisterUserResource;

class RegisterController extends AppBaseController
{   
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository= $authRepository;

    }

    public function registerUser(Request $request)
    {
        try {
            $register=$this->authRepository->registerUser($request);
           if($register){
              return $this->sendResponse(RegisterUserResource::collection($register),__('notification.notification_pvyeatpl'));
          }
          return $this->sendError(__('responses.notification_scptl'));
        } catch (\Throwable $th) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }
    
}
