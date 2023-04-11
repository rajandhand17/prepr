<?php
namespace App\Repositories\Api\Auth;
use App\Models\User;

class AuthRepository implements AuthInterface{

    private $user;
    function __construct(User $user)
    {
        $this->user=$user;
    }

    public function login($request)
    {
        try{
            return $this->user->login($request);
         }
         catch (\Exception $e){

            return false;
         }
    }

    public function verifyTwoFactor($request)
    {
        try{
            return $this->user->verifyTwoFactor($request);
         }
         catch (\Exception $e){

            return false;
         }
    }

    public function register($request)
    {
        try{
            return $this->user->register($request);
         }
         catch (\Exception $e){
             dd($e);

            return false;
         }
    }

    public function checkUsername($request)
    {
      try{
            return $this->user->checkUsername($request);
         }catch (\Exception $e){
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
      } catch (\Exception $e){
        return false;
      }
    }

    public function sendOtp($request)
    {
        try {
            return $this->user->sendOtp($request);
          } catch (\Exception $e){
            return false;
          }

    }

    public function verifyOtp($request)
    {
        try {
            return $this->user->verifyOtp($request);
        }catch(\Exception $e){
            return false;
          }
    }

    public function referalCode($request)
    {
        try {
            return $this->user->referalCode($request);
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

}

?>
