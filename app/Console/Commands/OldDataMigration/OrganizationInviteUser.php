<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\OrganizationInviteUser as OrganizationInviteUsers;

class OrganizationInviteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string 
     */
    protected $signature = 'migrate-old-data:organization-invite-user';

    /** 
     * The console command description. 
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old organization invite user table data to new db structure.';

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

            $this->info('Migrating old data for organization invite user table.');
            DB::beginTransaction();

            $organization_invite_user = DB::connection('mysql2')->table('organization_invite_user')->get();
            if($organization_invite_user->count() > 0){
                
                foreach ($organization_invite_user as $key => $single_organization_invite_user){
                   $organization_invite_user_details=[
                        'organisation_id' => $single_organization_invite_user->organisation_id,
                        'user_id' => $single_organization_invite_user->user_id,
                        'inviter_id' => $single_organization_invite_user->inviter_id,
                        'challenge_ids' => $single_organization_invite_user->challenge_ids,
                        'lab_ids' => $single_organization_invite_user->lab_ids,
                        'resource_ids' => $single_organization_invite_user->resource_ids,
                        'role' => $single_organization_invite_user->role,
                        'email' => $single_organization_invite_user->email,
                        'status' => $single_organization_invite_user->status,
                        'invite_type' => $single_organization_invite_user->invite_type,
                        'invitation_status' => $single_organization_invite_user->invitation_status,
                        'invite_status' => $single_organization_invite_user->invite_status,
                        'email_status' => $single_organization_invite_user->email_status,
                        'email_responce' => $single_organization_invite_user->email_responce,
                        'email_resend_status' => $single_organization_invite_user->email_resend_status,
                        'subject_line' => $single_organization_invite_user->subject_line,
                        'email_message' => $single_organization_invite_user->email_message,
                        'fail_schedule' => $single_organization_invite_user->fail_schedule,
                    ];
                    $check_organization_invite_user = OrganizationInviteUsers::where($organization_invite_user_details)->first();
                    if(!$check_organization_invite_user){
                        OrganizationInviteUsers::create($organization_invite_user_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for organisation table completed.');
                return;
            }
            DB::rollback();
            $this->error('No organisation found.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
