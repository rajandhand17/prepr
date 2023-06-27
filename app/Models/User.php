<?php

namespace App\Models;

use App\Helpers\SendMailHelper;
use App\Helpers\UtilityHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laratrust\Traits\LaratrustUserTrait;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

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
        'referal_code',
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

    public function userPersonal()
    {
        return $this->hasOne(UserPersonal::class);
    }

    public function userSetting()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**login apis */
    public function login($request)
    {
        try {
            /**checking user exists or not */
            $user = User::where('email', $request->email)->first();
            if ($user->verified_user == 0) {
                $response = ['success' => false, 'message' => __('notification.notification_pvyeatpl')];

                return $response;
            }
            if ($user) {
                /**check password same or not */
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken(env('APP_NAME'))->accessToken;

                    if ($user->two_factor_verification == 1) {
                        $otp = random_int(1000, 9999);
                        DB::beginTransaction();
                        $user->otp = $otp;
                        $user->save();
                        DB::commit();
                        /**sending otp on registeres number */
                        $data = ['subject' => 'Two Factor Verification', 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'otp' => $user['otp']];
                        $mail = SendMailHelper::sendMail($user, 'email.two_factor_otp', $data);
                        if ($mail) {
                            return ['success' => true, 'message'=> __('responses.two_factor_otp'), 'code' => 2];
                        }

                        return ['success' => false, 'message' => __('responses.failed_email'), 'code'=>null];
                    }
                    $data = User::where('email', $request->email)->first();
                    $response = ['success' => true,  'user' => $data, 'code' => 3, 'token' => $token, 'message' => __('responses.user_login_sucess')];

                    return $response;
                } else {
                    $response = ['success' => false, 'message'=>__('notification.notification_icpta'), 'code' => 4];

                    return $response;
                }
            } else {
                $response = ['success' => false, 'message'=>__('responses.notification_usernot_found'), 'code' => 5];

                return $response;
            }
        } catch (\Exception $e) {
            $response = ['success' => false, 'message'=>__('responses.send_error'), 'code' => 6];

            return $response;
        }
    }

    /**Verify two factor */
    public function verifyTwoFactor($request)
    {
        try {
            /**checking user exists or not */
            $user = User::where(['email' => $request->email, 'otp' => $request->otp])->first();
            if ($user) {
                $token = $user->createToken(env('APP_NAME'))->accessToken;
                $response = ['success' => true, 'token' => $token];

                return $response;
            } else {
                $response = ['success' => false, 'code' => 1];

                return $response;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**Register user */
    public function register($request)
    {
        try {
            DB::beginTransaction();
            $name = $request->first_name.' '.$request->last_name;
            $otp = random_int(1000, 9999);
            $string = Str::random(30);
            $referencecode = $request->username.Carbon::now()->format('Y');
            $user = new User();
            $user->preferred_language = $request->language;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->country_code = ($request->has('country_code')) ? $request->country_code : null;
            $user->phone_number = ($request->has('phone_number')) ? $request->phone_number : null;
            $user->otp = $otp;
            $user->verify_token = $string;
            $user->referal_code = $referencecode;
            $user->save();
            $user->attachRole('user');
            $member_manager = MemberManagement::where('email', $request->email)->get();
            if ($member_manager) {
                foreach ($member_manager as $member) {
                    $user->attachRole($member->role, $member->module_id);
                    $member_manager = MemberManagement::where('id', $member->id)->update(['invite_status'=>'1']);
                }
            }

            if ($user->id) {
                if ($request->register_type == 'organization') {
                    $organization = new Organization();
                    $organization->slug = UtilityHelper::generateSlug($request->organization_name, $organization);
                    $organization->user_id = $user->id;
                    $organization->title = $request->organization_name;
                    $organization->save();
                    $user = User::find($user->id);
                    $user->attachRole('organization_owner', $organization->id);
                    $request->user_type = 'employee';
                }
                $userpersonal = UserPersonal::create($user, $request);
                $usersetting = UserSetting::create($user, $request);
                if ($userpersonal && $usersetting) {
                    $data = ['subject' => 'Verify Your Email', 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'otp' => $user->otp];
                    $mail = SendMailHelper::sendMail($user, 'email.verify_otp', $data);
                    if ($mail) {
                        DB::commit();
                        /**sending otp on registeres email */
                        $userresponse = User::get()->where('email', $user->email);
                        $success = ['success' => true, 'user' => $userresponse];

                        return $success;
                    }
                    DB::rollback();

                    return ['success' => false, 'message' => __('responses.failed_email')];
                }
                DB::rollback();

                return ['success' => false, 'message' => __('responses.failed_registeration')];
            }
            DB::rollback();

            return ['success' => false, 'message' => __('responses.failed_registeration')];
        } catch (\Exception $e) {
            DB::rollback();

            return ['success' => false, 'message' => 'Something went wrong.'];
        }
    }

    /**Check user exists or not */
    public function checkUsername($request)
    {
        try {
            $username = User::select('id')->where('username', $request->username)->first();
            if ($username) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**Check email exists or not */
    public function checkEmail($request)
    {
        try {
            $checkemail = User::select('id')->where('email', $request->email)->first();
            if ($checkemail) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**Check phone number exists or not */
    public function checkPhone($request)
    {
        try {
            $checkphone = User::select('id')->where('phone_number', $request->phone)->first();
            if ($checkphone) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendOtp($request)
    {
        try {
            /**getting records of user by using email */
            $user = User::where(['email' => $request->email])->first();
            if ($user != '') {
                /**generating otp */
                $otp = random_int(1000, 9999);
                $user->otp = $otp;
                if ($user->save()) {
                    /**sending otp for forget password*/
                    if ($request->purpose === 'forget_password') {
                        $data = ['subject' => 'Forget Password', 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'otp' => $user->otp];
                        $mail = SendMailHelper::sendMail($user, 'email.forget_password_otp', $data);
                        if ($mail) {
                            $response = ['success' => true, 'purpose' => 'forget_password', 'code' => 1];

                            return $response;
                        }

                        return ['success' => false, 'message' => __('responses.failed_email')];
                    }
                    /**sending otp for verify email*/
                    if ($request->purpose === 'verify_email') {
                        $data = ['subject' => 'Verify Your Email', 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'otp' => $user->otp];
                        $mail = SendMailHelper::sendMail($user, 'email.verify_otp', $data);
                        if ($mail) {
                            $response = ['success' => true, 'purpose' => 'verify_email', 'code' => 2];

                            return $response;
                        }

                        return ['success' => false, 'message' => __('responses.failed_email')];
                    }
                    /**send otp for two factor verification */
                    if ($request->purpose === 'two_factor_verification') {
                        $data = ['subject' => 'Two Factor Verification', 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'otp' => $user['otp']];
                        $mail = SendMailHelper::sendMail($user, 'email.two_factor_otp', $data);
                        if ($mail) {
                            $response = ['success' => true, 'purpose' => 'two_factor_verification', 'code' => 3];

                            return $response;
                        }

                        return ['success' => false, 'message' => __('responses.failed_email')];
                    }

                    return false;
                } else {
                    return false;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**Verify otp */
    public function verifyOtp($request)
    {
        try {
            /**get records of particular user by using email */
            $user = User::select('id', 'otp', 'verified_user', 'country_code', 'phone_number', 'updated_at')->where(['email' => $request->email])->first();
            $updated_user = $user->updated_at;
            /**check user account verified or not */
            if ($user->verified_user === '1') {
                $response = ['success' => false, 'message' => __('responses.verify_success_already'), 'code' => 1];

                return $response;
            }
            /**Matching otp is same or not */
            if ($user->otp == $request->otp) {
                $user->email_verified_at = Carbon::now();
                $user->verified_user = '1';
                if ($user->save()) {
                    $data = ['subject' => 'Verified Successfully!', 'first_name' => $user->first_name, 'last_name' => $user->last_name];
                    $mail = SendMailHelper::sendMail($user, 'email.verified_successfully', $data);
                    if ($mail) {
                        $success = ['success' => true, 'user' => $user, 'code' => 2];

                        return $success;
                    }

                    return ['success' => false, 'message' => __('responses.failed_email'), 'code' => 3];
                }

                return false;
            } else {
                $response = ['success' => false, 'message' =>__('responses.otp_correct_required'), 'code' => 4];

                return $response;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**check referal code exists or not */
    public function referalCode($request)
    {
        try {
            $userrecords = User::select('id', 'email', 'first_name', 'last_name')->where(['referal_code' => $request->referal_code])->first();
            if ($userrecords) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**Forget Password */
    public function forgetPassword($request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return false;
            } else {
                $string = Str::random(60);
                $otp = random_int(1000, 9999);
                $user->remember_token = $string;
                $user->otp = $otp;
                $user->save();
                $data = ['subject' => 'Forget Password', 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'otp' => $user['otp']];
                $mail = SendMailHelper::sendMail($user, 'email.forget_password_otp', $data);
                if ($mail) {
                    $success = ['success' => true, 'user' => $user, 'code' => 1];

                    return $success;
                }

                return ['success' => false, 'message' => __('responses.failed_email'), 'code' => 2];
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**Reset password */
    public function resetPassword($request)
    {
        try {
            /**get records of particular user by using email */
            $user = User::where(['email' => $request->email])->first();
            /**check user account verified or not */
            if ($user->verified_user == 0) {
                $response = ['success' => false, 'message' =>__('responses.account_not_verified'), 'code' => 1];

                return $response;
            }
            /**checking otp same or not */
            if ($user->otp == $request->otp) {
                $user->password = Hash::make($request->password);
                if ($user->save()) {
                    $data = ['subject' => 'Reset Password Successfull!', 'first_name' => $user['first_name'], 'last_name' => $user['last_name']];
                    $mail = SendMailHelper::sendMail($user, 'email.reset_password', $data);
                    if ($mail) {
                        $success = ['success' => true, 'user' => $user];

                        return $success;
                    }

                    return ['success' => false, 'message' => __('responses.failed_email'), 'code' => 2];
                }
            } else {
                $response = ['success' => false, 'message' =>__('responses.otp_correct_required'), 'code' => 3];

                return $response;
            }

            return false;
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }
}
