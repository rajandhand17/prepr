<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laratrust\Traits\LaratrustUserTrait;
use App\Helpers\SMSHelper;
use Carbon\Carbon;
use Mail;
use App\Mail\SendMail;

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
            /**checking user exists or not */
            $user = User::where('email', $request->email)->first();
            if(!$user){
                return 1;
            }
            if($user->is_verify==0){
                return 2;
            }
            if($user){
                /**check password same or not */
              if (Hash::check($request->password, $user->password)) {
                  $token = $user->createToken(env("APP_NAME"))->accessToken;
                  $response = ['status'=>'success','token' => $token];
                  return $response;
              }else {
                return false;
              }
            }else{
                return false;
             }
        }catch (\Exception $e) {
            return false;
        }  
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
            $sms=SMSHelper::SendSms($receiver,$otp);
            if($user_id){
              DB::commit();
              $success=["success"=>true,"user"=>$user];
              return $success;
            }
            return false;
        }catch (\Exception $e){
            DB::rollback();
            return false;
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
                
                return response()->json(['status' => 'fail', 'message' =>__("responses.username_unique")], 200);
              }
              
              return response()->json(['status' => 'success', 'message' =>__("responses.username_available")], 200);
            }catch (\Exception $e){
               
                return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
            }
        }
        
        public function checkEmail($request)
        {
          try {
            $checkemail=User::select("id")->where("email",$request->email)->first();

            if($checkemail){
             return response()->json(['status'=>"fail","message"=>__("responses.unique_email")],200);
            }
            return response()->json(["status"=>"success","message"=>__("responses.not_exists_email")],200);
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
                /**getting records of user by using email */
                $user=User::select("two_factor_otp","is_verify","country_code","phone_number")->where(["email"=>$request->email])->first();
                if($user!=""){
                    /**check account is verified or not */
                    if($user->is_verify==1){
                        return response()->json(["status"=>"failed","message"=>__("responses.verify_success_already")]);
                    }
                    /**generating otp */
                    $otp=self::generateRandomnumber(4);
                    $saveData = [
                        "two_factor_otp" => $otp,
                    ];
                    /**save otp in database */
                    $userupdate=User::updateOrCreate(["email"=>$request->email], $saveData);
                    $receiver=$user->country_code.$user->phone_number;  
                    /**sending sms */
                    $sms=SMSHelper::SendSms($receiver,$otp);
                    if($sms){
                      return response()->json(["status"=>"success","message"=>__("responses.otp_send"),"data"=>["email"=>$request->email,"phone_number"=>$receiver]],200);
                    }
                }
                return response()->json(['status'=>"fail","message"=>__("notification.notification_swwptal")]);
            } catch (\Exception $e){
              
                return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
            }
        }

        public function verifyOtp($request)
        {   
            try {
                /**get records of particular user by using email */
                $user=User::select("id","two_factor_otp","is_verify","country_code","phone_number","updated_at")->where(["email"=>$request->email])->first();
                $updated_user=$user->updated_at;
                /**check user account verified or not */
                if($user->is_verify==1){
                    return response()->json(["status"=>"failed","message"=>__("responses.verify_success_already")]);      
                }
                $currentTime = Carbon::now();
                $minutes = $currentTime->diffInMinutes($updated_user);
                /**check otp time 10 minutes expired or not */
                if($minutes > 10){
                    return response()->json(['status'=>"fail","message"=>__("responses.otp_expried_required")]);
                }
                /**Matching otp is same or not */
                if($user->two_factor_otp==$request->otp){
                    $user = User::firstOrCreate(['id' => $user->id]);
                    $user->is_verify = '1';
                    if($user->save()){
                       return response()->json(["status"=>"success","message"=>__("responses.verify_success")],200);
                    }
                    return response()->json(['status'=>"fail","message"=>__("notification.notification_swwptal")],500);
                 }else{
                    return response()->json(['status'=>"fail","message"=>__("responses.otp_correct_required")],500);
                 }
            }catch (\Exception $e){
                return response()->json(["status"=>"fail","message"=>$e->getMessage()]);
            }
        }

        public function referalCode($request)
        {   
             try {
                $userrecords=User::select("id","email","first_name","last_name")->where(["mycode"=>$request->mycode])->first();
                if($userrecords){
                    return response()->json(['status' => 'success', 'message' =>__("responses.found_reference_code"), 'data' => $userrecords], 200);

                }
                 return response()->json(["status"=>"failed","message"=>"This reference code is not found!"],200);
                 }catch (\Exception $e) {  
                    return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
                 }
        }

        public function forgetPassword($request)
        {
            try{
                 $user=User::where("email",$request->email)->first();
                 if(!$user){
                   return response()->json(['status' => 'fail', 'message' =>__("notification.notification_yuoeanr")]);
                 }else{
                    $string = self::generateRandomString(60);
                    $otp=self::generateRandomnumber(4);
                    $user->remember_token = $string;
                    $user->two_factor_otp=$otp;
                    $user->save();
                    $mail=Mail::to($user->email)->send(new SendMail($user));
                    return response()->json(['status' => 'success', 'message' =>__("notification.notification_yprlsoyrea")], 200);
                }
            }catch(\Exception $e){
                return response()->json(["status"=>"fail","message"=>$e->getMessage()],200);
            }
        }

        public function resetPassword($request)
        {
            try {
                $user=User::where("email",$request->email)->first();
                $user->password = Hash::make($request->password);
                if($user->save()){
                    return true;
                 }
                return false;
            }catch(\Exception $e){
                return $this->sendError(__('responses.send_error'),500);
            }
        }
}

