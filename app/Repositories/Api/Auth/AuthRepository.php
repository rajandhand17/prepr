<?php
namespace App\Repositories\Api\Auth;
use App\Models\User;

class AuthRepository implements AuthInterface{
  
    private $user;
    function __construct(User $user)
    {
        $this->user=$user;
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
}
 
?>