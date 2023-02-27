<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\User as users;
use App\Models\UserPersonal;
use App\Models\UserSetting;
use Illuminate\Console\Command;
use DB;
use PhpParser\Builder\Class_;

Class User extends Command
{  
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:users';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old tag table data to new db structure.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for users table.');
            DB::beginTransaction();
            $users = DB::connection('mysql2')->table('users')->get();
            if($users->count() > 0){
                foreach ($users as $key => $single_user){
                    $language="";
                    $twofactor="";
                    if($single_user->language_id===1 || $single_user->language_id===0){
                        $language="en";
                    }elseif($single_user->language_id===1){
                        $language="fr";
                    }
                    if($single_user->two_factor==="allow"){
                         $twofactor=1;
                    }else{
                         $twofactor=0;
                    }
                     
                   $users_details=[
                        'preferred_language'=>$language,    
                        'first_name' => $single_user->first_name,
                        'last_name' => $single_user->last_name,
                        'full_name' => $single_user->name,
                        'username' => $single_user->username,
                        'email' => $single_user->email,
                        'password'=>$single_user->password,
                        'country_code'=>$single_user->country_code,
                        'phone_number'=>$single_user->phone_number,
                        'two_factor_verification'=>$twofactor,
                        'otp'=>$single_user->two_factor_otp,
                        'profile_image'=>$single_user->profile_image,
                        'referal_code'=>$single_user->referal_code,
                        'remember_token'=>$single_user->remember_token,
                    ];
                    $check_users = User::where($users_details)->first();
                    if(!$check_users){
                        $user=User::create($users_details);
                        $user_address=[
                            "user_id"=>$single_user->user_id,
                            "latitude"=>$single_user->latitude,
                            "longitude"=>$single_user->longitude,
                            "address"=>$single_user->address,
                            "city"=>$single_user->city,
                            "state"=>$single_user->state,
                            "country"=>$single_user->country,
                        ];
                        $user_personal=UserSetting::create($user_address);
                        if($user_personal){
                           $user_setting=[
                              "project_privacy" => $single_user->project_privacy,
                              "manage_alerts"=>$single_user->manage_alerts,
                              "is_subscribe"=>$single_user->is_subscribe,
                              "newsfeeds"=>$single_user->newsfeeds,
                              "email_subscription_notification"=>$single_user->email_subscription_notification,
                              "email_subscription_network_summary"=>$single_user->email_subscription_network_summary,
                              "email_subscription_challenge_summary"=>$single_user->email_subscription_challenge_summary,
                              "display_lab_minionboarding"=>$single_user->display_lab_minionboarding,
                              "display_challenge_minionboarding"=>$single_user->display_challenge_minionboarding,
                              "display_org_minionboarding"=>$single_user->display_org_minionboarding,
                              "fcm_notification_permission"=>$single_user->fcm_notification_permission,
                              "fcm_device_token"=>$single_user->fcm_device_token,
                           ];
                           $user_setting=UserSetting::create($user_setting);
                           if(!$user_setting){
                            DB::rollback();
                           }
                           
                        }else{
                            DB::rollback();
                        }                  
                    }
                }
                DB::commit();
                $this->info('Migrating of old data for users table completed.');
                return;
            }
            DB::rollback();
            $this->error('No user found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }


}