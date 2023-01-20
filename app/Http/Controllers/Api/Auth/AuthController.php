<?php

namespace App\Http\Controllers\Api\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Http\Requests\Auth\CheckUserRequest;
use App\Http\Requests\Auth\CheckEmailRequest;
use App\Http\Requests\Auth\CheckOrganizationRequest;
use App\Http\Requests\Auth\CheckPhoneRequest;
use App\Repositories\Api\Auth\AuthRepository;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;


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
                return $this->sendResponse($register,__('notification.registeration_successfully'));
               }
          return $this->sendError(__('responses.registeration_failed'));
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        } 
    }

    public function checkUsername(CheckUserRequest $request)
    {
        try {
            $username=$this->authRepository->checkUsername($request);
            if($username){
                return $this->sendResponse($username,__('responses.found_username_list'));
            }
           return $this->sendError(__('responses.not_found_username_list'));
        }catch (\Exception $e){
            return $this->sendError(__('responses.send_error'),500);
        } 

    }

    public function checkEmail(CheckEmailRequest $request)
    {
        try {
            $checkemail=$this->authRepository->checkEmail($request);
            if($checkemail){
                return $this->sendResponse($checkemail,__('responses.found_exists_email_list'));
            }
            return $this->sendError(__('responses.not_found_exists_email_list'));

        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function checkPhone(CheckPhoneRequest $request)
    {
        try {
            $checkphone=$this->authRepository->checkPhone($request);
            if($checkphone){
                  return $this->sendResponse($checkphone,__('responses.found_exists_phone_list'));
            }
            return $this->sendError(__('responses.not_found_exists_phone_list'));    

        } catch (\Exception $e) {
           return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function checkOrgnization(CheckOrganizationRequest $request)
    {
        try {
            $checkorganization=$this->authRepository->checkOrgnization($request);
            if($checkorganization){
               return $this->sendResponse($checkorganization,__('responses.found_exists_organizations'));
            }
            return $this->sendError(__('responses.not_found_exists_organizations_lists'));
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }
    }

    public function sendOtp(SendOtpRequest $request)
    {  
        try {
            $sendotp=$this->authRepository->sendOtp($request);
            if($sendotp!==""){
                return $this->sendResponse($sendotp,__('responses.sms_success'));
               }
               return $this->sendError(__('responses.sms_error'));
            } catch (\Exception $e) {
                return $this->sendError(__('responses.send_error'),500);
            }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    { 
      try {
        $verify=$this->authRepository->verifyOtp($request);
        if($verify!==""){
            return $this->sendResponse($verify,__('responses.verify_success'));
           }
           return $this->sendError(__('responses.verify_error'));
        }catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'),500);
        }  
    }
}
