<?php
namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\User as Users;

class User extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old user table data to new db structure.';

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
            $this->info('Migrating old data for user table.');
            DB::beginTransaction();
            $users = DB::connection('mysql2')->table('users')->get();
            if($users->count() > 0){
                foreach ($users as $key => $single_user){
                   $users_details=[ 
                        'device_token' => $single_user->device_token,
                        'name' => $single_user->name,
                        'first_name' => $single_user->first_name,
                        'last_name' => $single_user->last_name,
                        'username' => $single_user->username,
                        'email' => $single_user->email,
                        'verification' => $single_user->verification,
                        'country_code' => $single_user->country_code,
                        'two_factor' => $single_user->two_factor,
                        'two_factor_otp' => $single_user->two_factor_otp,
                        'password' => $single_user->password,
                        'profile_image' => $single_user->profile_image,
                        'phone_number' => $single_user->phone_number,
                        'fr_accept' => $single_user->fr_accept,
                        'fr_request' => $single_user->fr_request,
                        'point' => $single_user->point,
                        'rank' => $single_user->rank,
                        'is_verify' => $single_user->is_verify,
                        'remember_token' => $single_user->remember_token,
                        'is_email_sent' => $single_user->is_email_sent,
                        'verify_token' => $single_user->verify_token,
                        'mycode' => $single_user->mycode,
                        'isReferralOpen' => $single_user->isReferralOpen,
                        'manage_alerts' => $single_user->manage_alerts,
                        'is_subscribe' => $single_user->is_subscribe,
                    ];
                    $check_users = Users::where($users_details)->first();
                    if(!$check_users){
                        Users::create($users_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for users table completed.');
                return;
            }
            DB::rollback();
            $this->error('No users found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
