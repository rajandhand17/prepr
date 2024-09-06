<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\MagnetHelper;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Auth\CheckEmailRequest;
use App\Http\Requests\Auth\CheckPhoneRequest;
use App\Http\Requests\Auth\CheckUsernameRequest;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\Auth\MagnetSSOLoginFormRequest;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Http\Requests\Auth\ResetPasswordFormRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\SSOLoginFormRequest;
use App\Http\Requests\Auth\VerifyInviteCodeRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\VerifyTwoFactorRequest;
use App\Http\Requests\Public\User\UpdateFcmTokenFormRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\OrganizationCustomizationResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Models\UserActivity;
use App\Repositories\Api\Auth\AuthRepository;

class AuthController extends AppBaseController
{
    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth API - Login"},
     *     summary="Send request for check login",
     *     operationId="login",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Enter email of related to account!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="Enter password of account!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *
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
            if ($login['success'] == true) {
                if ($login['code'] === 2) {
                    $response = ['message' => $login['message'], 'code' => $login['code']];

                    return $this->sendResponse($response, $login['message'], 200);
                }
                if ($login['code'] === 3) {
                    UserActivity::logActivity($login['user']->id, 'login');

                    $response = ['token' => LoginResource::make(json_decode(json_encode($login), false)), 'user' => UserResource::make($login['user']), 'code' => $login['code']];

                    return $this->sendResponse($response, $login['message'], 200);
                }
            }
            if ($login['success'] == false) {
                return $this->sendError($login['message'], 401);
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify-two-factor",
     *     tags={"Auth API - Verify Two Factor"},
     *     summary="Send request for check Verify Two Factor",
     *     operationId="verifytwofactor",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Enter email of related to account!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         description="Enter two factor otp!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *
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
    public function twoFactorVerification(VerifyTwoFactorRequest $request)
    {
        try {
            $verifytwofactor = $this->authRepository->twoFactorVerification($request);
            if ($verifytwofactor['success'] == true) {
                UserActivity::logActivity($verifytwofactor['user']->id, 'login');
                $response = ['token' => LoginResource::make(json_decode(json_encode($verifytwofactor), false)), 'user' => UserResource::make($verifytwofactor['user']), 'code' => $verifytwofactor['code']];

                return $this->sendResponse($response, __('responses.user_login_success'), 200);
            }
            if ($verifytwofactor['success'] == false) {
                if ($verifytwofactor['code'] === 1) {
                    return $this->sendError(__('responses.otp_correct_required'), 401);
                }
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth API - Check Register"},
     *     summary="Send request for check register",
     *     operationId="registerUser",
     *
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge id for registered!",
     *         required=true,
     *         explode=true,
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
     *         description="Enter first name for registered!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="Enter last name for registered!",
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
     *         name="device_platform",
     *         in="query",
     *         description="Enter device platform for registered!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="user_type",
     *         in="query",
     *         description="Enter user type for registered!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="purpose",
     *         in="query",
     *         description="Enter purpose for registered!",
     *         required=true,
     *         explode=true,
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
     *         name="register_type",
     *         in="query",
     *         description="Enter the register type for registered!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *
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
            if ($register['success'] == false) {
                return $this->sendError($register['message'], 401);
            }
            if ($register['success'] == true) {
                return $this->sendResponse(null, __('responses.registration_success'), 200);
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/forget-password",
     *     tags={"Auth API - Forget Password"},
     *     summary="send email for forget-password",
     *     description="send otp for forget-password",
     *     operationId="forgetPassword",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email uses for send otp",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
            if ($forgetpassword['success'] == false) {
                return $this->sendError($forgetpassword['message'], 401);
            }
            if ($forgetpassword['success'] == true) {
                return $this->sendResponse([], __('responses.send_otp_success'), 200);
            }
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/checkusername",
     *     tags={"Auth API - Username"},
     *     summary="Send request for check user name",
     *     operationId="checkUsername",
     *
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="Check user name exists or not!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge for checkusername!",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
            if ($username == false) {
                return $this->sendResponse(null, __('responses.username_available'), 200);
            } else {
                return $this->sendError(__('responses.username_not_available'), 403);
            }

        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/checkemail",
     *     tags={"Auth API - Check Email"},
     *     summary="Send request for check check email",
     *     operationId="checkEmail",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="check the email value that exists or not!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge for email!",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/checkphone",
     *     tags={"Auth API - Check Phone"},
     *     summary="Send request for check email",
     *     operationId="checkPhone",
     *
     *     @OA\Parameter(
     *         name="phone_number",0
     *         in="query",
     *         description="check the phone number that exists or not!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge for checkphone!",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
                return $this->sendError(__('responses.already_registered_phone_number'), 403);
            }
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/send-otp",
     *     tags={"Auth API - Send Otp"},
     *     summary="Send request for send otp",
     *     operationId="sendOtp",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Uses emails to get account!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge id for send-otp!",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
            if ($send_otp['success'] === true) {
                if ($send_otp['purpose'] === 'forget_password') {
                    return $this->sendResponse([], __('responses.send_otp_success'), 200);
                }
                if ($send_otp['purpose'] === 'verify_email') {
                    return $this->sendResponse([], __('responses.user_verify_email_otp'), 200);
                }
                if ($send_otp['purpose'] === 'two_factor_verification') {
                    return $this->sendResponse([], __('responses.check_otp_email'), 200);
                }
            }
            if ($send_otp['success'] === false) {
                return $this->sendError($send_otp['message'], 401);
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify-otp",
     *     tags={"Auth API - Verify Otp"},
     *     summary="Verifing Otp",
     *     operationId="verifyOtp",
     *
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
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge id for verify otp!",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
    public function verifyAccount(VerifyOtpRequest $request)
    {
        try {
            $verify = $this->authRepository->verifyAccount($request);
            if ($verify['success'] === true) {
                $response = ['token' => LoginResource::make(json_decode(json_encode($verify), false)), 'user' => UserResource::make($verify['user'])];

                return $this->sendResponse($response, $verify['message'], 200);
            }
            if ($verify['success'] === false) {
                return $this->sendError($verify['message'], 208);
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify-invite-code",
     *     tags={"Auth API - Verify Referal Code"},
     *     summary="Send request for verify otp",
     *     operationId="referalCode",
     *
     *     @OA\Parameter(
     *         name="referral_code",
     *         in="query",
     *         description="check the referal code value that exists or not!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge id for referal-code!",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
    public function referralCode(VerifyInviteCodeRequest $request)
    {
        try {
            $referencecode = $this->authRepository->referralCode($request);
            if ($referencecode == true) {
                return $this->sendResponse(null, __('responses.verify_reference_success'), 200);
            }
            if ($referencecode == false) {
                return $this->sendError(null, __('responses.referral_code_not_exists'), 404);
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/reset-password",
     *     tags={"Auth API - Reset Password"},
     *     summary="Send request for reset password",
     *     operationId="resetPassword",
     *
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
     *     @OA\Parameter(
     *         name="otp",
     *         in="query",
     *         description="Enter otp for reset password!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Enter langauge for reset password!",
     *         required=true,
     *         explode=true,
     *     ),
     *
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
    public function resetPassword(ResetPasswordFormRequest $request)
    {
        try {
            $resetcode = $this->authRepository->resetPassword($request);
            if ($resetcode['success'] === true) {
                return $this->sendResponse(null, __('responses.success_reset_password'), 200);
            }
            if ($resetcode['success'] === false) {
                return $this->sendError($resetcode['message'], 403);
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/SSOlogin",
     *     tags={"Auth API - SSO Login"},
     *     summary="Send request for check SSO login",
     *     operationId="SSO login",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Enter email of related to account!",
     *         required=true,
     *         explode=true,
     *     ),
     *     @OA\Parameter(
     *         name="sso_type",
     *         in="query",
     *         description="Enter sso type of account!",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *     @OA\Parameter(
     *         name="language",
     *         in="query",
     *         description="Language values that needed to be considered for choose languages",
     *         required=true,
     *         explode=true,
     *
     *     ),
     *
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
    public function ssoLogin(SSOLoginFormRequest $request)
    {
        try {
            $ssorequest = $this->authRepository->ssoLogin($request);
            if ($ssorequest['success'] == true) {
                $response = ['token' => LoginResource::make(json_decode(json_encode($ssorequest), false)), 'user' => UserResource::make($ssorequest['user']), 'code' => $ssorequest['code']];

                return $this->sendResponse($response, $ssorequest['message'], 200);
            }
            if ($ssorequest['success'] == false) {
                return $this->sendError($ssorequest['message'], 401);
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function magnetSsoLogin(MagnetSSOLoginFormRequest $request)
    {
        try {
            $authorizationCode = $request->code;
            $token = MagnetHelper::getTokenFromMagnet($authorizationCode);
            if (!$token) {
                return $this->sendError(__('responses.unauthorized'), 400);
            }

            $magnetUser = MagnetHelper::getMagnetUser($token);

            if (!$magnetUser) {
                return $this->sendError(__('responses.magnet_unauthenticated'), 402);
            }

            $tokenResponse = $this->authRepository->magnetSsoLogin($magnetUser, $token);
            if ($tokenResponse['success']) {
                $response = ['token' => $tokenResponse['token'], 'user' => UserResource::make($tokenResponse['user']), 'code' => $tokenResponse['code']];

                return $this->sendResponse($response, $tokenResponse['message'], 200);
            }

            return $this->sendError($tokenResponse['message'], 401);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getOTPForAutomation($email)
    {
        try {
            if ($email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) {
                    $checkUser = $this->authRepository->getOtp($email);
                    if ($checkUser) {
                        if (isset($checkUser['code']) && $checkUser['code'] == 1) {
                            return $this->sendError(__('responses.not_exists_email'), 402);
                        }
                        $response = [
                            'otp' => $checkUser,
                        ];

                        return $this->sendResponse($response, 'success');
                    }

                    return $this->sendError(__('responses.send_error'), 500);
                }

                return $this->sendError(__('responses.valid_email_pattern'), 402);
            }

            return $this->sendError(__('responses.email_field_required'), 402);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function updateFcmToken(UpdateFcmTokenFormRequest $request)
    {
        try {
            $updateFCMToken = $this->authRepository->updateFcmToken($request);
            if ($updateFCMToken) {
                return $this->sendResponse($updateFCMToken, 'success');
            }

            return $this->sendError(__('responses.send_error'), 500);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function organizationCustomLoginRegistration($custom_url)
    {
        try {
            $checkOrganizationCustomizationData = UtilityHelper::checkComponentSlugExistOrNot('organization', $custom_url);
            if ($checkOrganizationCustomizationData) {
                if ($checkOrganizationCustomizationData->customization_login_register) {
                    return $this->sendResponse(OrganizationCustomizationResource::make($checkOrganizationCustomizationData), __('responses.found_organization_customization'));
                }
            }

            return $this->sendError(__('responses.not_found_organization_customization'), 404);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
