<?php
namespace App\Repositories\Api\Auth;
use App\Models\User;
use App\Models\Organisation;

class AuthRepository implements AuthInterface{
  
    private $user;
    private $organisation;
    function __construct(User $user,Organisation $organisation)
    {
        $this->user=$user;
        $this->organisation=$organisation;
    }

    public function register($request)
    {   
        try{ 
            return $this->user->register($request);
         }
         catch (\Exception $e){
           
            return false;
         }
    }

    public function login($request)
    {
        # code...
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

    public function checkOrgnization($request)
    {
        try {
            return $this->organisation->checkorgnization($request);
        } catch (\Exception $e) {
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
}
 
?>