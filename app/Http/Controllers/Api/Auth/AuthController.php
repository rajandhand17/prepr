<?php

namespace App\Http\Controllers\Api\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\RegisterFormRequest;
use App\Repositories\Api\Auth\AuthRepository;
use App\Http\Resources\Auth\RegisterUserResource;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

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
            $register=$this->authRepository->registerUser($request);
            if($register){
                return $this->sendResponse($register,__('notification.notification_pvyeatpl'));
               }
          return $this->sendError(__('responses.notification_scptl'));
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }


    public function login(Request $request)
    {
        $register_url = session()->get('url.intended');

            $validation_array = [
                'username' => 'required',
                'password' => 'required',
            ];
            $validation = Validator::make($request->all(), $validation_array);
            $messages = [
                'username.required' => __('notification.notification_reg_unr'),
                'password.required' => __('notification.notification_reg_pass')
            ];
            $validation = Validator::make($request->all(), $validation_array, $messages);
            if ($validation->fails()) {
                return response()->json(
                    ['status_code' => '0', 'success_message' => $validation->messages()->first()],
                    200
                );
            }
    }
}
