<?php

namespace App\Repositories\Api\Auth;

use App\Models\User;
use App\Services\UserService;

class AuthRepository implements AuthInterface
{
    private $user;
    private $userService;

    public function __construct(User $user,UserService $userService)
    {
        $this->user = $user;
        $this->userService = $userService;
    }

    public function login($request)
    {
        try {
            return $this->user->login($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function twoFactorVerification($request)
    {
        try {
            return $this->user->twoFactorVerification($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function register($request)
    {
        try {
            return $this->user->register($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkUsername($request)
    {
        try {
            return $this->user->checkUsername($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkEmail($request)
    {
        try {
            return $this->user->checkEmail($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkPhone($request)
    {
        try {
            return $this->user->checkPhone($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendOtp($request)
    {
        try {
            return $this->user->sendOtp($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyAccount($request)
    {
        try {
            return $this->user->verifyAccount($request);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function referralCode($request): bool
    {
        try {
            return $this->user->referralCode($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function forgetPassword($request)
    {
        try {
            return $this->user->forgetPassword($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function resetPassword($request)
    {
        try {
            return $this->user->resetPassword($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function ssoLogin($request)
    {
        try {
            return $this->user->ssoLogin($request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function magnetSsoLogin($magnetUserDetails)
    {
        try {
            return $this->user->magnetSsoLogin($magnetUserDetails);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getOtp($email)
    {
        try {
            return $this->user->getOtp($email);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateFcmToken($request)
    {
        try {
            return $this->userService->updateFcmToken($request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
