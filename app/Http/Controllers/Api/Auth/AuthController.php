<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Http\Requests\Auth\CheckUsernameRequest;
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
use App\Http\Resources\Auth\LoginResource;
class AuthController extends AppBaseController
{
    private $authRepository;
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth API -login"},
     *     summary="Send request for check login",
     *     operationId="login",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Enter email of related to account!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Enter password of account!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function login(LoginFormRequest $request)
    {
        try {
            $login = $this->authRepository->login($request);
            if($login==2){
                return $this->sendError(__('notification.notification_pvyeatpl'), 403);
            }
            if($login == 9){
                return $this->sendResponse(null, __('responses.otp_send'), 200);
            }
            if($login['status'] == "success"){
                return $this->sendResponse($login['token'], __('responses.user_login_sucess'), 200);
            }
            if($login['status'] == "false"){
                return $this->sendError(__('responses.incorrect_password'), 401);
            }
            return $this->sendError(__('responses.send_error'), 500);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth API - check register"},
     *     summary="Send request for check register",
     *     operationId="registerUser",
     *     @OA\Parameter(
     *         name="language_id",
     *         in="query",
     *         description="Enter langauge id for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="Enter username for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Enter email for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="Enter first name of user for registered",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="Enter last name of user for registered",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Enter password for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="confirm password same as password!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="user_type",
     *         in="query",
     *         description="Enter user type for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Enter the status for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="country_code",
     *         in="query",
     *         description="Enter the country code for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         description="Enter the phone number for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="organization_name",
     *         in="query",
     *         description="Enter the organization name for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="vanity_link",
     *         in="query",
     *         description="Enter the vanity link for registered!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function registerUser(RegisterFormRequest $request)
    {
        try {
            $register = $this->authRepository->register($request);
            if ($register == false) {
                return $this->sendError(__('notification.notification_swwptal'), 401);
            }
            if ($register['success'] == "success") {
                return $this->sendResponse($register, __('responses.user_register'), 200);
            }
            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/forget-password",
     *     tags={"Auth API -forget-password"},
     *     summary="send email for forget-password",
     *     description="send otp for forget-password",
     *     operationId="forgetPassword",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email uses for send otp",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        try {
            $forgetpassword = $this->authRepository->forgetPassword($request);
            if ($forgetpassword == false) {
                return $this->sendError(__('notification.notification_swwptal'), 401);
            }
            if ($forgetpassword == true) {
                return $this->sendResponse(null, __('notification.notification_yprlsoyrea'), 200);
            }
            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/auth/checkusername",
     *     tags={"Auth API - Username"},
     *     summary="Send request for check user name",
     *     operationId="checkUsername", 
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="Check user name exists or not!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function checkUsername(CheckUsernameRequest $request)
    {
        try {
            $username = $this->authRepository->checkUsername($request);
            if($username == false){
                return $this->sendResponse(null, __('responses.username_available'), 200);
            }else{
                return $this->sendError(__('responses.username_unique'), 403);
            }
            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/auth/checkemail",
     *     tags={"Auth API - Check Email"},
     *     summary="Send request for check check email",
     *     operationId="checkEmail",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="check the email value that exists or not!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function checkEmail(CheckEmailRequest $request)
    {
        try {
            $checkemail = $this->authRepository->checkEmail($request);
            if ($checkemail == false) {
                return $this->sendResponse(null, __('responses.not_exists_email'), 200);
            } else {
                return $this->sendError(__('responses.unique_email'), 403);
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/checkphone",
     *     tags={"Auth API - Check Phone"},
     *     summary="Send request for check email",
     *     operationId="checkPhone",
     *     @OA\Parameter(
     *         name="phone_number",
     *         in="query",
     *         description="check the phone number that exists or not!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function checkPhone(CheckPhoneRequest $request)
    {
        try {
            $checkphone = $this->authRepository->checkPhone($request);
            if ($checkphone == false) {
                return $this->sendResponse(null, __('responses.found_exists_phone_list'), 200);
            } else {
                return $this->sendError(__("responses.already_number"), 403);
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/checkorgnization",
     *     tags={"Auth API - Check Orgnization"},
     *     summary="Send request for check Organization",
     *     operationId="checkOrgnization",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="check organization name that exists or not!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */

    public function checkOrgnization(CheckOrganizationRequest $request)
    {
        try {
            $checkorganization = $this->authRepository->checkOrgnization($request);
            if ($checkorganization == false) {
                return $this->sendResponse(null, __('responses.organization_not_exists'));
            } elseif ($checkorganization == true) {
                return $this->sendError(__('responses.organization_exists'), 403);
            }
            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/send-otp",
     *     tags={"Auth API - Send Otp"},
     *     summary="Send request for send otp",
     *     operationId="sendOtp",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Uses emails to get account!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function sendOtp(SendOtpRequest $request)
    {
        try {
            $send_otp = $this->authRepository->sendOtp($request);
            if($send_otp==5){
                return $this->sendError(__('notification.notification_uarvrf'), 403);
            }
            if($send_otp==true){
                return $this->sendResponse(null, __('responses.otp_send'), 200);
            }
            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify-otp",
     *     tags={"Auth API - Verify Otp"},
     *     summary="Verifing Otp",
     *     operationId="verifyOtp",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Uses email to get account details!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         description="Uses otp for verify account!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $verify = $this->authRepository->verifyOtp($request);
            if ($verify == 5) {
                return $this->sendError(__('notification.notification_uarvrf'), 403);
            }
            if ($verify == 4) {
                return $this->sendError(__('responses.otp_expried_required'), 403);
            }
            if ($verify == true) {
                return $this->sendResponse(null, __('responses.verify_success'), 200);
            }
            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify-invite-code",
     *     tags={"Auth API - Verify Otp"},
     *     summary="Send request for verify otp",
     *     operationId="referalCode",
     *     @OA\Parameter(
     *         name="mycode",
     *         in="query",
     *         description="check the mycode value that exists or not!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function referalCode(VerifyInviteCodeRequest $request)
    {
        try{
            $referencecode = $this->authRepository->referalCode($request);
            if($referencecode==true){
                return $this->sendResponse(null, __('responses.verify_reference_success'), 200);
            }
            if($referencecode==false){
                return $this->sendResponse(null, __('responses.verify_reference_error'), 200);
            }
            return $this->sendError(__('responses.send_error'), 500);
        }catch(\Exception $e){
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/auth/reset-password",
     *     tags={"Auth API - Reset Password"},
     *     summary="Send request for reset password",
     *     operationId="resetPassword",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email used for get details of account!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Check the password value that exists or not!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *    @OA\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="Check the password value that exists or not!",
     *         required=true,
     *         explode=true,
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *    
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found!",
     *    
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request!",
     *    
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error!",
     *    
     *     ),
     * )
     */
    public function resetPassword(submitResetPasswordFormRequest $request)
    {
        try {
            $resetcode = $this->authRepository->resetPassword($request);
            if ($resetcode == 5){
                return $this->sendError(__('responses.account_not_verified'), 403);
            }
            if($resetcode == 4){
                return $this->sendError(__('responses.otp_expried_required'), 403);
            }
            if($resetcode==true) {
                return $this->sendResponse(null, __('responses.verify_success'), 200);
            }
            if ($resetcode == true) {
                return $this->sendResponse(null, __('notification.notification_yprs'), 200);
            }
            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
