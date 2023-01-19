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

    public function checkusername($request)
    {
      try{ 
            return $this->user->checkusername($request);
         }catch (\Exception $e){
           
            return false;
         }
    }

    public function checkemail($request)
    {
        try {
            return $this->user->checkemail($request);
        } catch (\Exception $e) {
            return false;
        }
       
    }

    public function checkphone($request)
    {
      try {
        return $this->user->checkphone($request);
      } catch (\Exception $e){
        return false;
      }
    }

    public function checkorgnization($request)
    {
        try {
            return $this->organisation->checkorgnization($request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
 
?>