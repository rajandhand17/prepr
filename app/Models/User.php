<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SMSHelper;
use Carbon\Carbon;

class User extends Authenticatable
{
    use LaratrustUserTrait;
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

    public function login($request)
    {    
        try {
            $user = User::where('email', $request->email)->first();
            if($user) {
              if (Hash::check($request->password, $user->password)) {
                  $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                  $response = ['token' => $token];
                  return response($response, 200);
              } else {
                  $response = ["message" => "Password mismatch"];
                  return response($response, 422);
              }
            }else{
              $response = ["message" =>'User does not exist'];
              return response($response, 422);
           }
        }catch (\Exception $e) {
           return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }  
   }

   public function logout ($request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
    
    public function register($request)
    {   
        try {
            DB::beginTransaction();
            $name=$request->first_name." ".$request->last_name;
            $otp=self::generateRandomnumber(4);
            $sting = self::generateRandomString(30);
            $receiver=$request->country_code.$request->phone_number;
            $user=new User;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->name = $name;
            $user->first_name=$request->first_name;
            $user->last_name=$request->last_name;
            $user->remember_token = $sting;
            $user->verify_token = $sting;
            $user->mycode = $request->username;
            $user->password = Hash::make($request->password);
            $user->phone_number = $request->phone_number;
            $user->country_code = $request->country_code;
            $user->two_factor_otp=$otp;
            $user->save();
            $user_id = $user->id;
            $user_personals=new UserPersonal;
            $user_personals->user_id=$user_id;
            $user_personals->status=$request->status;
            $user_personals->language=$request->language_id;
            $user_personals->save();
            /**sending otp on registeres number */
            $sms=SMSHelper::sendsms($receiver,$otp);
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
             /**generating string  */
        function generateRandomString($length = 30) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
             /**generating numerice value */
        function generateRandomnumber($length = 4) {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        public function checkUsername($request)
        {  
           try {
            $username=User::select("id")->where("username",$request->username)->first();
             
            if($username){
                
                return response()->json(['status' => 'fail', 'message' => 'This username is not avaible!'], 200);
              }
              
              return response()->json(['status' => 'success', 'message' => 'Username available!'], 200);
            }catch (\Exception $e){
               
                return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
            }
        }
        
        public function checkEmail($request)
        {
          try {
            $checkemail=User::select("id")->where("email",$request->email)->first();
            if($checkemail){
              
             return response()->json(['status'=>"fail","message"=>"This email is already  exists!"],200);
            }
            return response()->json(["status"=>"success","message"=>"This email is not registered with us!"],200);
          }catch (\Exception $e) {  
             return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
          }
        }

        public function checkPhone($request)
        {
            try {
                $checkphone=User::select("id")->where("phone_number",$request->phone)->first();
                if($checkphone){
                    return response()->json(['status'=>"fail","message"=>"This phone is already exists!"]);
                }
                 return response()->json(["status"=>"success","message"=>"This phone number is not registered with us!"]);
            } catch (\Exception $e){
                return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
            }
        }

        public function sendOtp($request)
        {
            try { 
                /**receing parameters */
                $countrycode=$request->country_code;
                $phone_number=$request->phone_number;
                $sendotp=User::select("two_factor_otp","is_verify")->where(["country_code"=>$request->country_code,"phone_number"=>$request->phone_number])->first();

               /**checking account is verified or not */
                if($sendotp->is_verify==1){
                return response()->json(["status"=>"success","message"=>"Your account is already verified,Please login!"],200);
                }
                $otp=self::generateRandomnumber(4);
                $saveData = [
                    "two_factor_otp" => $otp,
                ];         

            $userdata=User::updateOrCreate(["country_code"=>$request->country_code,"phone_number"=>$request->phone_number], $saveData);
                $receiver=$request->country_code.$request->phone_number;
                $sms=SMSHelper::sendsms($receiver,$otp);
                if($sms){
                    return response()->json(["status"=>"success","message"=>"Please check your phone for otp!"],200);
                }
            
                return response()->json(['status'=>"fail","message"=>"Please try later!"]);
            } catch (\Exception $e){
              
                return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
            }
        }

        public function verifyOtp($request)
        {
            try {
                /**receiving parameters */
                 $countrycode=$request->country_code;
                $phone_number=$request->phone_number;
                $sendotp=User::select("id","two_factor_otp","is_verify","updated_at")->where(["country_code"=>$request->country_code,"phone_number"=>$request->phone_number])->first();
                $start=$sendotp->updated_at;
                $end=$currentTime = Carbon::now();
                $minutes = $end->diffInMinutes($start);
                    /**checking otp expired or not */
                if($minutes > 10){
                  return response()->json(['status'=>"fail","message"=>"Otp is expired,Please again generate Otp!"]);
                }
                /**checking account is verified or not */
                if($sendotp->is_verify==1){
                    return response()->json(["status"=>"failed","message"=>"Your account is already verified,Please login !"]);      
                }
                /**Matching otp is same or not */
                if($sendotp->two_factor_otp==$request["otp"]){
                    $user = User::firstOrCreate(['id' => $sendotp->id]);
                    $user->is_verify = '1';
                    if($user->save()){
                     return response()->json(["status"=>"success","message"=>"Your account is verified successfully,Please login !"],200);
                    }
                    return response()->json(['status'=>"fail","message"=>"Something went wrong, Please try later!"]);
                 }else{
                    return response()->json(['status'=>"fail","message"=>"Please Enter correct otp!"]);
                 }
            }catch (\Exception $e){
                return response()->json(["status"=>"fail","message"=>$e->getMessage()]);
            }
        }

        public function referenceCode($request)
        {   
             try {
                $userrecords=User::select("id","email","first_name","last_name")->where(["mycode"=>$request->mycode])->first();
                if($userrecords){
                    return response()->json(['status' => 'success', 'message' => 'This reference code is found!.', 'data' => $userrecords], 200);
                }
                   return response()->json(["status"=>"success","message"=>"This reference code is not found!"],200);
                 }catch (\Exception $e) {  
                    return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
                 }
        }
}

