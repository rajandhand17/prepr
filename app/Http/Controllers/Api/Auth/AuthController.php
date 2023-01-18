<?php

namespace App\Http\Controllers\Api\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\RegisterFormRequest;
use App\Repositories\Api\Auth\AuthRepository;

class AuthController extends AppBaseController
{   
    private $authRepository="";
    public function __construct(AuthRepository $authRepository)
    {   
        $this->authRepository= $authRepository;
    }

    public function registerUser(RegisterFormRequest $request)
    {     
        try {
            $register=$this->authRepository->register($request);
            if($register){
                return $this->sendResponse($register,__('notification.notification_pvyeatpl'));
               }
          return $this->sendError(__('responses.notification_scptl'));
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        } 
    }
}
