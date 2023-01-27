<?php

namespace App\Http\Controllers\Api\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Http\Requests\Auth\CheckUserRequest;
use App\Http\Requests\Auth\CheckEmailRequest;
use App\Http\Requests\Auth\CheckOrganizationRequest;
use App\Http\Requests\Auth\CheckPhoneRequest;
use App\Http\Requests\Auth\LoginFormRequest;
use App\Repositories\Api\Auth\AuthRepository;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\VerifyInviteCodeRequest;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\submitResetPasswordFormRequest;
use Predis\Response\Status;

class AuthController extends AppBaseController
{   
    private $authRepository="";
    public function __construct(AuthRepository $authRepository)
    {   
        $this->authRepository= $authRepository;
    }

    public function login(LoginFormRequest $request)
    {    
        try {
        $login=$this->authRepository->login($request);
        $responses=json_decode($login->content());
        $data=$responses->data;
        if($responses->status=="success"){
                return $this->sendResponse($data,__('notification.login_successfully'));
        }else{
                return $this->sendError($responses->message);
        }
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        } 

    }

    public function registerUser(RegisterFormRequest $request)
    {     
        try {
            $register=$this->authRepository->register($request);
            $responses=json_decode($register->content());
            $data=$responses->data;
            if($responses->status=="success"){
                return $this->sendResponse($data,__('responses.user_register'));
            }else{
                return $this->sendError($responses->message);
            }
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        } 
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {   
        try{
           $forgetpassword=$this->authRepository->forgetPassword($request);
           $responses=json_decode($forgetpassword->content());
            $data=["email"=>$request->email];
            if($responses->status=="success"){
                return $this->sendResponse($data,__('notification.notification_yprlsoyrea'));
            }else{
                return $this->sendError($responses->message);
            }
         }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function checkUsername(CheckUserRequest $request)
    {
        try {
            $username=$this->authRepository->checkUsername($request);
            $responses=json_decode($username->content());
            $data=["username"=>$request->username];
            if($responses->status=="success"){
                return $this->sendResponse($data,__('responses.username_available'));
            }else{
                return $this->sendError($responses->message);
            }
            }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        } 

    }

    public function checkEmail(CheckEmailRequest $request)
    {
        try {
            $checkemail=$this->authRepository->checkEmail($request);
            $responses=json_decode($checkemail->content());
            $data=["email"=>$request->email];
            if($responses->status=="success"){
                return $this->sendResponse($data,__('responses.not_exists_email'));
            }else{
                  return $this->sendError($responses->message);
             }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function checkPhone(CheckPhoneRequest $request)
    {
        try {
            $checkphone=$this->authRepository->checkPhone($request);
            $responses=json_decode($checkphone->content());
            $data=["phone_number"=>$request->phone_number];
            if($responses->status=="success"){
                return $this->sendResponse($data,__('responses.found_exists_phone_list'));
            }else{
                  return $this->sendError($responses->message);
             }
        } catch (\Exception $e) {
           return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function checkOrgnization(CheckOrganizationRequest $request)
    {   
        try {
            $checkorganization=$this->authRepository->checkOrgnization($request);
            $responses=json_decode($checkorganization->content());
            $data=["name"=>$request->name];
            if($responses->status=="success"){
                return $this->sendResponse($data,__('responses.organization_not_exists'));
            }else{
                  return $this->sendError($responses->message);
             }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function sendOtp(SendOtpRequest $request)
    {  
        try {
            $sendotp=$this->authRepository->sendOtp($request);
            $responses=json_decode($sendotp->content());
            $data=$responses->data;
            if($responses->status=="success"){
                return $this->sendResponse($data,__('responses.otp_send'));
            }else{
                  return $this->sendError($responses->message);
             }
            }catch (\Exception $e) {
                return $this->sendError(__('responses.send_error'),500);
            }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {   
      try {
        $verify=$this->authRepository->verifyOtp($request);
        $responses=json_decode($verify->content(), true);
        $data=["email"=>$request->email,"otp"=>"$request->otp"];
        if($responses['status']=="success"){
            return $this->sendResponse($data,__('responses.verify_success'));
         }else{
            return $this->sendError($responses['message']);
         }
        }catch(\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }  
    }
    
    public function referalCode(VerifyInviteCodeRequest $request)
    {
       try {
         $referencecode=$this->authRepository->referalCode($request);
         $responses=json_decode($referencecode->content(), true);
         $data=$responses['data'];
         if($responses['status']=="success"){
            return $this->sendResponse($data,__('responses.verify_reference_success'));
         }else{
            return $this->sendError($responses['message']);
         }
       }catch (\Exception $e) {
        return $this->sendError(__('responses.send_error'),500);
       }
    }

    public function resetPassword(submitResetPasswordFormRequest $request)
    {
        try {
            $resetcode=$this->authRepository->resetPassword($request);
            $responses=json_decode($resetcode->content(), true);
            $data=["email"=>$request->email];
            if($responses['status']=="success"){
               return $this->sendResponse($data,__('notification.notification_yprs'));
            }else{
               return $this->sendError($responses['message']);
            }
        } catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        }
    }
   
}
