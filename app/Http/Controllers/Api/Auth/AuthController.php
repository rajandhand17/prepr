<?php

namespace App\Http\Controllers\Api\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\RegisterDataRequest;
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

   public function registerUser(Request $request)
    {   
        try {
            $request_data = $request->all();
            $validation_msg = [
                'password.regex' => __('notification.notification_tpmbcilol'),
                'username.regex' => __('notification.notification_reg_unc'),
            ];
            $validation_array = [
                'first_name' => 'required|string|max:20',
                'last_name' => 'required|string|max:20',
                'username' => 'required|string|max:20|regex:/^[A-Za-z0-9-_-]*$/|unique:users',
                'email' => 'required|string|email|max:50|unique:users',
                'phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
                'password' => 'required|string|min:8|max:14|regex:/^(?=.*?[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*?[#?!@$%.^&*-]).+$/',
                'password_confirmation' => 'required|string|same:password',
                'user_type' => 'required|string',
            ];

            $validation_msg += [
                'first_name.required'    => __('notification.notification_reg_fnr'),
                'first_name.max:20'      => __('notification.notification_reg_tfnmbg'),
                'last_name.required'     => __('notification.notification_reg_lnr'),
                'last_name.max:20'       => __('notification.notification_reg_tlnmbg'),
                'username.required'     => __('notification.notification_reg_unr'),
                'username.max:20'       => __('notification.notification_reg_tunmbg'),
                'email.required'        => __('notification.notification_reg_er'),
                'phone_number.required' => __('notification.notification_reg_pnr'),
                'password.required'     => __('notification.notification_reg_pass'),
                'password_confirmation.required' => __('notification.notification_reg_cpr'),
                'user_type.required'    => __('notification.notification_reg_utr'),
            ];
            if (isset($request->status)) {
                $validation_array   =   array_merge(['status' => 'required|string|min:1'], $validation_array);
            }
            if (isset($request_data['role']) && $request_data['role'] == 'organization') {
                $validation_array   =   array_merge(['organization_name' => 'required|unique:organisations,name'], $validation_array);
            }

            $validator = Validator::make($request->all(), $validation_array, $validation_msg);

            
            if ($validator->fails()) {
                return response()->json(['status' => 'fail', 'message' => $validator->messages()->first()], 200);
            }
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
