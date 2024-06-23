<?php

namespace App\Models;

use App\Helpers\MagnetHelper;
use App\Helpers\SendMailHelper;
use App\Helpers\UtilityHelper;
use App\Jobs\Chargebee\CreateCustomerJob;
use App\Jobs\Chargebee\SubscribePlanJob;
use App\Services\Manage\MemberManagementService;
use App\Services\Manage\OrganizationService;
use App\Services\UserEducationService;
use App\Services\UserExperienceService;
use Carbon\Carbon;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use SoftDeletes;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'preferred_language',
        'preferred_organization',
        'first_name',
        'last_name',
        'full_name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'country_code',
        'phone_number',
        'two_factor_verification',
        'otp',
        'profile_image',
        'user_points',
        'user_rank',
        'verified_user',
        'verify_token',
        'referral_code',
        'is_profile_completed',
        'remember_token',
        'is_deactivated',
        'is_onboarding_completed',
        'go1_id',
        'go1_user_metadata',
        'magnet_user_id',
        'magnet_user_role',
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

    protected $casts = ['go1_user_metadata' => 'object'];

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.'.$this->id;
    }

    public function getProfileImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }

    public function userPersonal()
    {
        return $this->hasOne(UserPersonal::class);
    }

    public function userSetting()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function userPoints()
    {
        return $this->hasMany(UserPoint::class);
    }

    public function userLabs()
    {
        return $this->hasMany(Lab::class, 'user_id', 'id');
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class, 'user_id', 'id');
    }

    public function userFollow()
    {
        return $this->hasMany(Friend::class, 'reference_id', 'id')
            ->where('reference_follow', '2')
            ->orWhere(function ($query) {
                $query->where(['user_id' => $this->id, 'user_follow' => '2']);
            });
    }

    public function userFriends()
    {
        return $this->hasMany(Friend::class, 'reference_id', 'id')
            ->where('status', '1')
            ->orWhere(function ($query) {
                $query->where('user_id', $this->id)
                    ->where('status', '1');
            });
    }

    public function userRequestSend()
    {
        return $this->hasMany(Friend::class, 'user_id', 'id')->where('status', '0');
    }

    public function requestReceived()
    {
        return $this->hasMany(Friend::class, 'reference_id', 'id')->where('status', '0');
    }

    public function userFollowRequest()
    {
        return $this->hasMany(Friend::class, 'user_id', 'id')->where('user_follow', '0');
    }

    public function followRequestSent()
    {
        return $this->hasMany(Friend::class, 'reference_id', 'id')
            ->where('user_follow', '1')
            ->orWhere(function ($query) {
                $query->where(['user_id' => $this->id, 'reference_follow' => '1']);
            });
    }

    public function followRequestReceived()
    {
        return $this->hasMany(Friend::class, 'reference_id', 'id')
            ->where('reference_follow', '1')
            ->orWhere(function ($query) {
                $query->where(['user_id' => $this->id, 'user_follow' => '1']);
            });
    }

    public function userAddress()
    {
        return $this->hasOne(UserAddress::class, 'user_id', 'id');
    }

    public function userExperience()
    {
        return $this->hasMany(UserExperience::class);
    }

    public function userEducation()
    {
        return $this->hasMany(UserEducation::class);
    }

    public function userPatents()
    {
        return $this->hasMany(UserPatent::class);
    }

    public function userSkills()
    {
        return $this->hasMany(UserSkills::class)->where('pinned', '0');
    }

    public function userJobs()
    {
        return $this->hasMany(UserJob::class)->where('pinned', '0');
    }

    public function userTags()
    {
        return $this->hasMany(UserTag::class, 'user_id', 'id');
    }

    public function userPinnedSkills()
    {
        return $this->hasMany(UserSkills::class)->where('pinned', '1');
    }

    public function userCertificates()
    {
        return $this->hasMany(UserCertificate::class);
    }

    public function userPersonalFiles()
    {
        return $this->hasMany(UserPersonalFile::class);
    }

    public function userResume()
    {
        return $this->hasOne(UserPersonalFile::class)->where('type', '0');
    }

    public function userProjects()
    {
        return $this->hasMany(Project::class, 'user_id', 'id');
    }

    /**login apis */
    public function login($request)
    {
        try {
            /**checking user exists or not */
            $user = User::where('email', $request->email)->first();
            if ($user->verified_user == 0) {
                $response = ['success' => false, 'message' => __('responses.verify_email')];

                return $response;
            }
            if ($user->is_deactivated == 1) {
                $response = ['success' => false, 'message' => __('responses.deactivated_account')];

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
                        $data = ['subject' => __('responses.email_subject_two_factor_verification'), 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'otp' => $user['otp']];
                        $mail = SendMailHelper::sendMail($user, 'email.two_factor_otp', $data);
                        if ($mail) {
                            return ['success' => true, 'message' => __('responses.two_factor_otp'), 'code' => 2];
                        }

                        return ['success' => false, 'message' => __('responses.failed_email'), 'code' => null];
                    }
                    $data = User::where('email', $request->email)->first();
                    $response = ['success' => true, 'user' => $data, 'code' => 3, 'token' => $token, 'message' => __('responses.user_login_success')];

                    return $response;
                } else {
                    $response = ['success' => false, 'message' => __('responses.invalid_credentials'), 'code' => 4];

                    return $response;
                }
            } else {
                $response = ['success' => false, 'message' => __('responses.user_not_found'), 'code' => 5];

                return $response;
            }
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => __('responses.send_error'), 'code' => 6];

            return $response;
        }
    }

    /**Verify two factor */
    public function twoFactorVerification($request)
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
            $user->password = ($request->sso_registration == 'no') ? Hash::make($request->password) : null;
            $user->country_code = ($request->has('country_code')) ? $request->country_code : null;
            $user->phone_number = ($request->has('phone_number')) ? $request->phone_number : null;
            $user->otp = $otp;
            $user->verify_token = $string;
            $user->referral_code = $referencecode;
            $user->save();
            $user->attachRole('user');
            $member_manager = MemberManagement::where('email', $request->email)->get();
            if ($member_manager) {
                foreach ($member_manager as $member) {
                    $user->attachRole($member->role, $member->module_id);
                    $member_manager = MemberManagement::where('id', $member->id)->update(['invite_status' => '1']);
                }
            }
            if ($user->id) {
                if ($request->register_type == 'organization') {
                    $organization = new Organization();
                    $organization->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $organization->slug = UtilityHelper::generateSlug($request->organization_title, $organization);
                    $organization->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $organization->user_id = $user->id;
                    $organization->title = $request->organization_title;
                    $organization->save();
                    $user = User::find($user->id);
                    $user->attachRole('organization_owner', $organization->id);
                    $request->user_type = 'employee';

                    // Jobs for creating customer and subscribe plan
                    $planDetail = config('chargebee.chargebee_plan.seed_plan_yearly'); //Default plan selected
                    CreateCustomerJob::withChain([
                        new SubscribePlanJob($user, $organization, $planDetail),
                    ])->dispatch($user);
                }
                $userpersonal = UserPersonal::create($user, $request);
                $usersetting = UserSetting::create($user, $request);
                if ($userpersonal && $usersetting) {
                    $data = ['subject' => __('responses.verify_your_email'), 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'otp' => $user->otp];
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

                return ['success' => false, 'message' => __('responses.failed_registration')];
            }
            DB::rollback();

            return ['success' => false, 'message' => __('responses.failed_registration')];
        } catch (\Exception $e) {
            DB::rollback();

            return ['success' => false, 'message' => __('responses.send_error')];
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
            $checkphone = User::select('id')->where('phone_number', $request->phone_number)->first();
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
                        $data = ['subject' => __('responses.email_subject_forget_password'), 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'otp' => $user->otp];
                        $mail = SendMailHelper::sendMail($user, 'email.forget_password_otp', $data);
                        if ($mail) {
                            $response = ['success' => true, 'purpose' => 'forget_password', 'code' => 1];

                            return $response;
                        }

                        return ['success' => false, 'message' => __('responses.failed_email')];
                    }
                    /**sending otp for verify email*/
                    if ($request->purpose === 'verify_email') {
                        $data = ['subject' => __('responses.verify_your_email'), 'first_name' => $user->first_name, 'last_name' => $user->last_name, 'otp' => $user->otp];
                        $mail = SendMailHelper::sendMail($user, 'email.verify_otp', $data);
                        if ($mail) {
                            $response = ['success' => true, 'purpose' => 'verify_email', 'code' => 2];

                            return $response;
                        }

                        return ['success' => false, 'message' => __('responses.failed_email')];
                    }
                    /**send otp for two factor verification */
                    if ($request->purpose === 'two_factor_verification') {
                        $data = ['subject' => __('responses.email_subject_two_factor_verification'), 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'otp' => $user['otp']];
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
    public function verifyAccount($request)
    {
        try {
            /**get records of particular user by using email */
            $user = User::where(['email' => $request->email])->first();
            /**check user account verified or not */
            if ($user->verified_user === '1') {
                $response = ['success' => false, 'message' => __('responses.verify_success_already'), 'code' => 1];

                return $response;
            }
            /**Matching otp is same or not */
            if ($user->otp == $request->otp) {
                $token = $user->createToken(env('APP_NAME'))->accessToken;
                $user->email_verified_at = Carbon::now();
                $user->verified_user = '1';
                if ($user->save()) {
                    $data = ['subject' => __('responses.email_subject_verified_successfully'), 'first_name' => $user->first_name, 'last_name' => $user->last_name];
                    $mail = SendMailHelper::sendMail($user, 'email.verified_successfully', $data);
                    if ($mail) {
                        $data = User::where('email', $request->email)->first();
                        $success = ['success' => true, 'user' => $data, 'token' => $token, 'message' => __('responses.user_login_success')];

                        return $success;
                    }

                    return ['success' => false, 'message' => __('responses.failed_email'), 'code' => 3];
                }

                return false;
            } else {
                $response = ['success' => false, 'message' => __('responses.otp_correct_required'), 'code' => 4];

                return $response;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**check referal code exists or not */
    public function referralCode($request)
    {
        try {
            $userrecords = User::select('id', 'email', 'first_name', 'last_name')->where(['referral_code' => $request->referral_code])->first();
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
                $response = ['success' => false, 'message' => __('responses.account_not_verified'), 'code' => 1];

                return $response;
            }
            /**checking otp same or not */
            if ($user->otp == $request->otp) {
                $user->password = Hash::make($request->password);
                if ($user->save()) {
                    $data = ['subject' => __('responses.email_subject_reset_password'), 'first_name' => $user['first_name'], 'last_name' => $user['last_name']];
                    $mail = SendMailHelper::sendMail($user, 'email.reset_password', $data);
                    if ($mail) {
                        $success = ['success' => true, 'user' => $user];

                        return $success;
                    }

                    return ['success' => false, 'message' => __('responses.failed_email'), 'code' => 2];
                }
            } else {
                $response = ['success' => false, 'message' => __('responses.otp_correct_required'), 'code' => 3];

                return $response;
            }

            return false;
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    /** SSO Login*/
    public function ssoLogin($request)
    {
        try {
            /**checking user exists or not */
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $ssoKey = null;
                switch ($request->sso_type) {
                    case 'google':
                        $ssoKey = '1';
                        break;
                    case 'linkedin':
                        $ssoKey = '2';
                        break;
                    case 'microsoft':
                        $ssoKey = '3';
                        break;
                    case 'apple':
                        $ssoKey = '4';
                        break;
                    case 'magnet':
                        $ssoKey = '5';
                        break;
                    default:
                        $ssoKey = null;
                        break;
                }

                if ($ssoKey === null) {
                    $response = ['success' => false, 'message' => __('responses.invalid_sso_type'), 'code' => 4];

                    return $response;
                }
                $token = $user->createToken(env('APP_NAME'))->accessToken;
                $checkSSODetails = UserSSOLogin::where(['user_id' => $user->id, 'sso_type' => $ssoKey])->first();
                if ($checkSSODetails == null) {
                    $usersso = UserSSOLogin::create($user, $request);
                } else {
                    $usersso = UserSSOLogin::where(['user_id' => $user->id, 'sso_type' => $request->sso_type])->update(['sub' => $request->sub, 'access_token' => $request->access_token]);
                }
                $response = ['success' => true, 'user' => $user, 'code' => 3, 'token' => $token, 'message' => __('responses.user_login_success')];

                return $response;
            } else {
                $response = ['success' => false, 'message' => __('responses.user_not_found'), 'code' => 5];

                return $response;
            }
        } catch (\Exception $e) {
            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function generateUniqueUsername($baseUsername)
    {
        $username = $baseUsername;
        $number = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername.$number;
            $number++;
        }

        return $username;
    }

    public function createOrFindMagnetUser($magnetUserDetails)
    {
        try {
            $user = User::where('email', data_get($magnetUserDetails, 'email'))->first();
            if (!$user) {
                // register users
                $baseUsername = explode('@', data_get($magnetUserDetails, 'email'))[0];
                $uniqueUserName = $this->generateUniqueUsername($baseUsername);

                return User::query()->create([
                    'first_name'     => data_get($magnetUserDetails, 'first_name'),
                    'last_name'      => data_get($magnetUserDetails, 'last_name'),
                    'username'       => $uniqueUserName,
                    'email'          => data_get($magnetUserDetails, 'email'),
                    'phone_number'   => data_get($magnetUserDetails, 'phone_number'),
                    'full_name'      => data_get($magnetUserDetails, 'first_name').' '.data_get($magnetUserDetails, 'last_name'),
                    'magnet_user_id' => data_get($magnetUserDetails, 'user_id'),
                    'verified_user'  => '1',
                ]);
            }

            return $user;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function magnetSsoLogin($magnetUserDetails, $accessToken)
    {
        try {
            DB::beginTransaction();
            $user = $this->createOrFindMagnetUser($magnetUserDetails);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => __('responses.unauthorized'),
                ];
            }

            UserSSOLogin::query()->updateOrCreate([
                'user_id'      => $user->id,
                'sso_type'     => config('constants.sso_type.magnet'),
                'access_token' => $accessToken,
            ]);

            if (!$user->magnet_user_id) {
                $user->magnet_user_id = data_get($magnetUserDetails, 'user_id');
                $user->save();
            }

            $roles = ['user'];
            $magnetUserRoles = [data_get($magnetUserDetails, 'role')];
            if (data_get($magnetUserDetails, 'user_types')) {
                foreach (data_get($magnetUserDetails, 'user_types') as $userType) {
                    $magnetUserRoles[] = data_get($userType, 'api_tag');
                    $roles[] = match (data_get($userType, 'api_tag')) {
                        'ORG_ADMIN', 'ORG_FULL_ADMIN', 'ORG_OWNER' => 'organization_owner',
                        default => 'user',
                    };
                }
            }
            $user->magnet_user_role = implode(',', array_filter($magnetUserRoles));

            if (data_get($magnetUserDetails, 'education')) {
                $addEducationDetails = UserEducationService::addMagnetEducationDetails($user, data_get($magnetUserDetails, 'education'));
                if (!$addEducationDetails) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'message' => __('response.unauthorized'),
                    ];
                }
            }

            if (data_get($magnetUserDetails, 'employment')) {
                $addEmploymentDetails = UserExperienceService::addMagnetUserExperience($user, data_get($magnetUserDetails, 'employment'));
                if (!$addEmploymentDetails) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'message' => __('response.unauthorized'),
                    ];
                }
            }
            // AUTO INVITE TO ORGANIZATION BASED ON MAGNET COMMUNITY ID
            $lmsUserInfo = MagnetHelper::getLMSUserInfo($accessToken);
            if (count($lmsUserInfo) > 0) {
                $communitiesIds = array_filter(array_map(function ($item) {
                    return data_get($item, 'affiliate_id');
                }, $lmsUserInfo));

                if (isset($communitiesIds[0])) {
                    $user->preferred_organization = $communitiesIds[0];
                }

                $formattedUsers = collect([[
                    'type'          => config('constants.member_management_type.invite'),
                    'invite_status' => '1',
                    'invite_type'   => config('constants.member_management_invite_type.email'),
                    'invitee_name'  => data_get($user, 'full_name'),
                    'invitee_email' => data_get($user, 'email'),
                    'role'          => 'User',
                ]]);
                $organizations = OrganizationService::getOrganizationBasedOnCommunityIds($communitiesIds);
                if ($organizations) {
                    $organizations->map(function ($organization) use ($formattedUsers) {
                        MemberManagementService::addMembers(
                            $organization,
                            'organization',
                            (object) [
                                'auto_invite'  => 'yes',
                                'email_status' => 'sent',
                                'subject_line' => 'Invitation to Learn Lab '.$organization->display_name,
                                'email_body'   => 'Welcome to the '.$organization->title.'! You will find a lot of the key information here, including the relevant challenges, resources, and discussion. Check back regularly for updates.',
                            ],
                            $formattedUsers
                        );
                    });
                }
            }

            $user->attachRoles(array_unique($roles));
            $user->save();
            $token = $user->createToken(env('APP_NAME'))->accessToken;
            DB::commit();

            return [
                'success' => true,
                'user'    => $user,
                'code'    => 3,
                'token'   => $token,
                'message' => __('responses.user_login_success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(__('responses.send_error'), 500);
        }
    }

    public function getOtp($email)
    {
        try {
            $user = User::where(['email' => $email])->first();
            if ($user) {
                return $user->otp;
            } else {
                $response = ['success' => false, 'code' => 1];

                return $response;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_users', 'user_id', 'conversation_id');
    }

    public function presence()
    {
        return $this->hasOne(ConversationUserPresenceStatus::class, 'user_id', 'id');
    }

    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
}
