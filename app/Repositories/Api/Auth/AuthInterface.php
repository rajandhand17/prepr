<?php

namespace App\Repositories\Api\Auth;

interface AuthInterface
{
    public function login($request);

    public function register($request);

    public function forgetPassword($request);

    public function checkUsername($request);

    public function checkEmail($request);

    public function checkPhone($request);

    public function sendOtp($request);

    public function verifyAccount($request);

    public function referralCode($request);

    public function resetPassword($request);

    public function twoFactorVerification($request);

    public function ssoLogin($request);

    public function magnetSsoLogin($magnetUserDetails, $token);
}
