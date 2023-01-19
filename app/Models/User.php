<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'country_code',
        'verification',
        'name',
        'email',
        'password',
        'two_factor',
        'two_factor_otp',
        'is_login',
        'profile_image',
        'phone_number',
        'fr_request',
        'fr_accept',
        'point',
        'rank',
        'remember_token',
        'is_verify',
        'is_email_sent',
        'verify_token',
        'mycode',
        'isReferralOpen',
        'manage_alerts',
        'is_subscribe',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
     
    public function UserPersonal()
    {
        return $this->hasOne(UserPersonal::class);
    }
    public function register($request)
    {   
        try {
            DB::beginTransaction();
            $name=$request['first_name']." ".$request['last_name'];
            $sting = self::generateRandomString(30);
            $user=new User;
            $user->username = $request['username'];
            $user->email = $request['email'];
            $user->name = $name;
            $user->first_name=$request['first_name'];
            $user->last_name=$request['last_name'];
            $user->remember_token = $sting;
            $user->verify_token = $sting;
            $user->mycode = $request['username'];
            $user->password = Hash::make($request['password']);
            $user->phone_number = $request['phone_number'];
            $user->save();
            $user_id = $user->id;
            $user_personals=new UserPersonal;
            $user_personals->user_id=$user_id;
            $user_personals->status=$request["status"];
            $user_personals->language=$request["language_id"];
            $user_personals->save();
            DB::commit();
            if($user_id){
              return response()->json(['status' => 'success', 'message' => 'You have registered successfully.', 'data' => $user], 200);
            }
           
            return response()->json(['status' => 'fail', 'message' => 'Something happened wrong, Please try later'], 200);
           
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }

        function generateRandomString($length = 30) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        public function checkusername($request)
        {  
           try {
            $username=User::select("id")->where("username",$request['username'])->first();
             
            if($username){
                
                return response()->json(['status' => 'fail', 'message' => 'This username is not avaible!'], 200);
              }
              
              return response()->json(['status' => 'success', 'message' => 'Username available!'], 200);
            }catch (\Exception $e){
               
                return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
            }
        }
        
        public function checkemail($request)
        {
          try {
            $checkemail=User::select("id")->where("email",$request['email'])->first();
            if($checkemail){
              
             return response()->json(['status'=>"fail","message"=>"This email is already  exists!"],200);
            }
            return response()->json(["status"=>"success","message"=>"This email is not registered with us!"],200);
          }catch (\Exception $e) {  
             return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
          }
        }

        public function checkphone($request)
        {
            try {
                $checkphone=User::select("id")->where("phone_number",$request['phone'])->first();
                if($checkphone){
                    return response()->json(['status'=>"fail","message"=>"This phone is already exists!"]);
                }
                 return response()->json(["status"=>"success","message"=>"This phone number is not registered with us!"]);
            } catch (\Exception $e){
                return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
            }
        }

      
   
}

