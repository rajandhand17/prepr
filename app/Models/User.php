<?php

namespace App\Models;

//use App\Http\Requests\RegisterDataRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Models\Setting;
use App\Models\UserPoint;
use App\Models\AutoCreateTemplate;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Event;

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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


   public function register($data)
    {     

        try {
            DB::beginTransaction();
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'country_code' => $data['country_code'], 
                'phone_number' => $data['phone_number'],
                'password' => $data['password'],
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'language_id' => $data['language_id'],
            ]);

            $language = Language::where('status', 1)->where('id', $data['language_id'])->first();

            $data['name'] = $data['first_name'] . ' ' . $data['last_name'];
            if (isset($data['referralCode']) && $data['referralCode']) {
                $user->referal_code = $data['referralCode'];
                $referal = User::where('mycode', $data['referralCode'])->first();
                if ($referal) {
                    $point_1 = Setting::get(config('points.referal_code'));
                    $point_2 = Setting::get(config('points.referal_user'));
                    UserPoint::firstOrCreate(['type' => 'referal_code', 'date' => date('Y-m-d'), 'user_id' => $user->id, 'point' => $point_2]);
                    UserPoint::firstOrCreate(['type' => 'referal_user', 'date' => date('Y-m-d'), 'user_id' => $referal->id, 'point' => $point_1]);
                }
            }

            $user->phone_number = (empty($data['phone_number'])) ? null : $data['phone_number'];

            if (isset($data['user_type'])) {
                //get autocreate templates
                $getcloneInfo = AutoCreateTemplate::where('role_user_type', $data['user_type'])->where('language', $language->lang_iso)->first();

                if ($getcloneInfo) {
                    $labGroupsArray = explode(',', @$getcloneInfo->lab_group_id);
                    $challengeGroupsArray = explode(',', @$getcloneInfo->challenge_group_id);
                    $groupLabIds = Group::whereIn('id', $labGroupsArray)->pluck('lab_id')->toArray();
                    $groupChallengeIds = Group::whereIn('id', $challengeGroupsArray)->pluck('challenge_id')->toArray();

                    $autoTemplateLabIds = explode(',', @$getcloneInfo->lab_id);
                    $autoTemplateChallengeIds = explode(',', @$getcloneInfo->challenge_id);
                    $reqlLabIds = array_filter(array_merge($autoTemplateLabIds, $groupLabIds));
                    $reqChallengeIds = array_filter(array_merge($autoTemplateChallengeIds, $groupChallengeIds));
                    $implodeLabIds = implode(',', $reqlLabIds);
                    $implodeChallengeIds = implode(',', $reqChallengeIds);
                    $finalLabIds = array_unique(explode(',', $implodeLabIds));
                    $finalChallengeIds = array_unique(explode(',', $implodeChallengeIds));
                } else {
                    $finalLabIds = [];
                    $finalChallengeIds = [];
                }

                // sync auto template challenges and labs for user registration
                if (!empty($finalLabIds)) {
                    foreach ($finalLabIds as $key => $labId) {
                        if (isset($data['normal_user']) && $data['normal_user'] == 'normal_user' && $labId != '' && $labId !== null) {
                            Helper::syncUserMembersForLabTemplates($user, $labId);
                        }
                    }
                }
                if (!empty($finalChallengeIds)) {
                    foreach ($finalChallengeIds as $key => $challengeId) {
                        if (isset($data['normal_user']) && $data['normal_user'] == 'normal_user' && $challengeId != '' && $challengeId !== null) {
                            Helper::syncUserMembersForChallengeTemplates($user->id, $user->email, $challengeId);
                        }
                    }
                }
            }
            $organization = collect();
            if (isset($data['role']) && $data['role'] == 'organization') {
                $user->syncRoles(['free_organisation_manager', 'user']);
                $user->admin_lab_limit = '1';
                $user->admin_challenge_limit = '1';
                $user->save();
                $languageIso = Helper::getLanguageIso($user->email);
                // create organization
                $org = Organisation::create(['user_id' => $user->id,'language' => $languageIso, 'name' => $data['organization_name'], 'vanity_slug' => $data['vanity_slug'],'slug' => $data['vanity_slug'], 'vanity_link' => URL::to('/') . '/org/' . $data['vanity_link'], 'status' => '1', 'plan' => 'limited', 'plan_validity' => 'yearly', 'labs_limit' => '1', 'challenges_limit' => '1']);

                // sync auto template challenges and labs for user registration
                if (!empty($finalChallengeIds)) {
                    foreach ($finalChallengeIds as $key => $challengeId) {
                        if ($challengeId != '' && $challengeId !== null) {
                            Helper::freeChallengemanagerSync($user->id, $org->id, $challengeId);
                            /*Helper:: inviteChallengeUsers('org', $data['user_type'], $challengeId, $user->name, $user->email);*/
                            // auto create adding as member
                            if (@$getcloneInfo->invite_challenges == '1') {
                                Helper::syncUserMembersForChallengeTemplates($user->id, $user->email, $challengeId);
                            }
                        }
                    }
                }
                if (!empty($finalLabIds)) {
                    foreach ($finalLabIds as $key => $labId) {
                        if ($labId != '' && $labId !== null) {
                            // auto create adding as member
                            if (@$getcloneInfo->invite_labs == '1') {
                                Helper::syncUserMembersForLabTemplates($user, $labId);
                            }
                            Helper::freeLabmanagerSync($user->id, $org->id, $labId);
                        }
                    }
                }

                UserPersonal::create([
                    'user_id' => $user->id,
                    'user_type' => $data['user_type'],
                ]);
            } elseif (isset($data['role']) && $data['role'] == 'free_lab_manager') {
                $user->syncRoles(['free_lab_manager', 'user']);
                $user->admin_lab_limit = 1;
                $user->admin_challenge_limit = 1;

                UserPersonal::create([
                    'user_id' => $user->id,
                    'user_type' => $data['user_type']
                ]);

                // sync auto template challenges and labs for user registration
                if (!empty($finalLabIds)) {
                    foreach ($finalLabIds as $key => $labId) {
                        if ($labId != '' && $labId !== null) {
                            Helper::freeLabmanagerSync($user->id, '', $labId);
                            if (@$getcloneInfo->invite_labs == '1') {
                                Helper::syncUserMembersForLabTemplates($user, $labId);
                            }
                        }
                    }
                }
                if (!empty($finalChallengeIds)) {
                    foreach ($finalChallengeIds as $key => $challengeId) {
                        if ($challengeId != '' && $challengeId !== null) {
                            Helper::freeChallengemanagerSync($user->id, '', $challengeId);
                            if (@$getcloneInfo->invite_challenges == '1') {
                                Helper::syncUserMembersForChallengeTemplates($user->id, $user->email, $challengeId);
                            }
                        }
                    }
                }
            } elseif (isset($data['role']) && $data['role'] == 'free_challenge_manager') {
                $user->syncRoles(['free_challenge_manager', 'user']);
                $user->admin_lab_limit = 1;
                $user->admin_challenge_limit = 1;

                UserPersonal::create([
                    'user_id' => $user->id,
                    'user_type' => $data['user_type']
                ]);

                // creating challenge for free challenge manager
                if (!empty($finalChallengeIds)) {
                    foreach ($finalChallengeIds as $key => $challengeId) {
                        if ($challengeId != '' && $challengeId !== null) {
                            Helper::freeChallengemanagerSync($user->id, '', $challengeId);
                            if (@$getcloneInfo->invite_challenges == '1') {
                                Helper::syncUserMembersForChallengeTemplates($user->id, $user->email, $challengeId);
                            }
                        }
                    }
                }
            } else {
                $user->syncRoles(['user']);
            }
            $sting = str_random(30);
            $user->remember_token = $sting;
            $user->verify_token = $sting;
            $user->mycode = trim($data['username']) . random_int('100', '999');
            $user->save();
            $user_id = $user->id;

            // Register user get org role
            $assignOrgRoles = Helper::syncRolesForIfInvitedFromOranisation($user->email);
            if (!empty($assignOrgRoles)) {
                if ($user->syncRoles($assignOrgRoles)) {
                    Helper::updateUserIdInviteByEmailInOrganisation($user->email, $user->id);
                }
            }

            // adding user personal details
            if ($user->hasRole('user')) {
                UserPersonal::updateOrCreate([
                    'user_id' => $user_id,
                    'status' => (isset($data['status'])) ? $data['status'] : 'looking_team',
                    'user_type' => $data['user_type'],
                ]);
            }

            // sync invite user to lab, challenge, project, and organisation
            Helper::invitedUserSync($data['email'], $user_id);

            // send user to sendgrid list as per user type
            $processSendGrid=$this->sendgridUserType($data, $user, $organization);
//            if ($processSendGrid==false) {
//                DB::rollback();
//                return false;
//            }
            $url = route('emailVerify', [$user->remember_token]);
            $data1 = [
                'mail_template' => 'email_verification', "username" => $user->name, "remember_token" => $user->remember_token,
                'email' => $user->email, 'to_email' => $user->email, 'url' => $url, 'to_name' => $user->username, 'fullname' => $user->name,
                'title' => __('labels.labels_reg_ve'), 'subject_title' => __('labels.labels_reg_ve'), 'name' => $user->name
            ];
            User::where('id', $user->id)->update(['is_email_sent' => '1']);
            if (!empty($user->email)) {
                Event::dispatch('send-template', array($data1));
            }
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
