<?php

namespace App\Repositories\Api\Auth;

interface AuthInterface
{
    public function login($request);

    public function twoFactorVerification($request);

    public function register($request);

    public function checkUsername($request);

    public function checkEmail($request);

    public function checkPhone($request);

    public function sendOtp($request);

    public function verifyAccount($request);

    public function verifyResetCode($request);

    public function referralCode($request);

    public function forgetPassword($request);

    public function resetPassword($request);

    public function ssoLogin($request);

    public function magnetSsoLogin($magnetUserDetails, $token);

    public function getOtp($email);

    public function updateFcmToken($request);
}
