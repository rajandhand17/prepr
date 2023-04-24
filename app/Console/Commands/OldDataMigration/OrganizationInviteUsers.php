<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\OrganizationInviteUser;

class OrganizationInviteUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:organization_invite_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old host table data to new db structure.';

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
            $this->info('Migrating old data for organization invite users table.');
            DB::beginTransaction();
            $hosts = DB::connection('mysql2')->table('organization_invite_user')->get();

            if($hosts->count() > 0){
                foreach ($hosts as $key => $single_invite_user){
                    
                    if($single_invite_user->role=="labmanager"){
                        $single_invite_user->role=3;
                    }elseif($single_invite_user->role=="challengemanager"){
                        $single_invite_user->role=4;
                    }elseif($single_invite_user->role=="user"){
                        $single_invite_user->role=6;
                    }elseif($single_invite_user->role=="resourcemanager"){
                        $single_invite_user->role=5;
                    }elseif($single_invite_user->role=="organisation_manager"){
                        $single_invite_user->role=2;
                    }elseif($single_invite_user->role=="projectevaluator"){
                        $single_invite_user->role=6;
                    }elseif($single_invite_user->role=="org_admin_manager"){
                        $single_invite_user->role=6;
                    }elseif($single_invite_user->role==null){
                        $single_invite_user->role=2;
                    }
                    if($single_invite_user->inviter_id==0){
                        continue;
                    }
                   $organization_invite_user_details=[
                        'organisation_id' => $single_invite_user->organisation_id,
                        'user_id' => $single_invite_user->user_id,
                        'inviter_id' => $single_invite_user->inviter_id,
                         'role' => $single_invite_user->role,
                        'email' => $single_invite_user->email,
                        'status' => $single_invite_user->status,
                        'invite_type' => $single_invite_user->invite_type,
                        'invitation_status' => $single_invite_user->invitation_status,
                        'invite_status' => $single_invite_user->invite_status,
                        'email_status' => $single_invite_user->email_status,
                        'subject_line' => $single_invite_user->subject_line,
                        'email_message' => $single_invite_user->email_message,
                        'fail_schedule' => $single_invite_user->fail_schedule,
                    ];
                    $check_hosts = OrganizationInviteUser::where($organization_invite_user_details)->first();
                    if(!$check_hosts){
                        OrganizationInviteUser::create($organization_invite_user_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for hosts table completed.');
                return;
            }
            DB::rollback();
            $this->error('No hosts found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
