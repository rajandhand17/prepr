<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;

class Role extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for assign role to user : Note : This command should be execute after migrated user and organization tables from legacy to learnlab.';

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
            $this->info('Started Migration from legacy to learnlab db for ORG-Roles + super-admin roles');
            DB::beginTransaction();

            //Get legacy super admin email for assign super admin role
            $superAdminUserEmails = null;
            $superAdminRoleIdFromSecondDB = DB::connection('mysql2')->table('roles')->where('name', 'superadmin')->value('id');
            if ($superAdminRoleIdFromSecondDB) {
                $superAdminUserEmails = DB::connection('mysql2')->table('model_has_roles')->join('users', 'model_has_roles.model_id', '=', 'users.id')->where('model_has_roles.role_id', $superAdminRoleIdFromSecondDB)->where('model_has_roles.model_type', 'App\\Models\\User')->pluck('users.email');
            }
            User::chunkById(1000, function ($users) use ($superAdminUserEmails) {
                foreach ($users as $user) {
                    if ($user->email) {
                        // Start for super-admin role migrate
                        if ($superAdminUserEmails) {
                            if ($superAdminUserEmails->contains($user->email)) {
                                $user->attachRole('super_admin');
                            }
                        }
                        //End for super-admin role migrate

                        // Start For Org role migrate
                        $roleOrder = ['organisation_manager', 'org_admin_manager', 'labmanager', 'challengemanager', 'resourcemanager', 'user'];
                        $orgRoles = DB::connection('mysql2')
                            ->table('organization_invite_user as oiu')
                            ->select('oiu.*')
                            ->where('oiu.email', $user->email)
                            ->whereIn('oiu.role', $roleOrder)
                            ->orderByRaw('FIELD(oiu.role, '.implode(',', array_map(function ($role) {
                                return "'$role'";
                            }, $roleOrder)).')')
                            ->get()
                            ->groupBy('organisation_id')
                            ->map(function ($group) {
                                return $group->first();
                            });
                        if ($orgRoles) {
                            foreach ($orgRoles as $orgRole) {
                                if ($orgRole->role) {
                                    if (Organization::where('id', $orgRole->organisation_id)->exists()) {
                                        switch ($orgRole->role) {
                                            case 'organisation_manager':
                                                $role = 'organization_manager';
                                                $roleName = config('constants.role_name.organization_manager');
                                                break;
                                            case 'org_admin_manager':
                                                $role = 'organization_owner';
                                                $roleName = config('constants.role_name.organization_owner');
                                                break;
                                            case 'labmanager':
                                                $role = 'lab_manager';
                                                $roleName = config('constants.role_name.lab_manager');
                                                break;
                                            case 'challengemanager':
                                                $role = 'challenge_manager';
                                                $roleName = config('constants.role_name.challenge_manager');
                                                break;
                                            case 'resourcemanager':
                                                $role = 'resource_manager';
                                                $roleName = config('constants.role_name.resource_manager');
                                                break;
                                            case 'user':
                                                $role = 'user';
                                                $roleName = config('constants.role_name.user');
                                                break;
                                        }

                                        switch ($orgRole->invite_type) {
                                            case 'email':
                                                $inviteType = '0';
                                                break;
                                            case 'network':
                                                $inviteType = '1';
                                                break;
                                            case 'integration':
                                                $inviteType = '4';
                                                break;
                                            case 'other':
                                                $inviteType = '0';
                                                break;
                                        }

                                        $conditions = [
                                            'type'      => '0',
                                            'module_id' => $orgRole->organisation_id,
                                            'email'     => $orgRole->email,
                                            'role'      => $roleName,
                                        ];

                                        $data = [
                                            'uuid'                => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                                            'invite_type'         => $inviteType ?? '0',
                                            'module_type'         => '0',
                                            'inviter_id'          => $orgRole->inviter_id,
                                            'auto_invite'         => '2',
                                            'invite_status'       => '1',
                                            'invitee_name'        => $user->full_name ?? null,
                                            'email_status'        => '1',
                                            'subject_line'        => $orgRole->subject_line ?? null,
                                            'email_body'          => $orgRole->email_message ?? null,
                                            'email_resend_status' => '0',
                                            'created_at'          => Carbon::createFromTimestamp($orgRole->created_at)->format('Y-m-d H:i:s'),
                                            'updated_at'          => Carbon::createFromTimestamp($orgRole->updated_at)->format('Y-m-d H:i:s'),
                                        ];
                                        $existingRecord = MemberManagement::where($conditions)->first();
                                        if ($existingRecord) {
                                            $existingRecord->update($data);
                                        } else {
                                            MemberManagement::create(array_merge($conditions, $data));
                                        }

                                        $user->attachRole($role, $orgRole->organisation_id);
                                    }
                                }
                            }
                        }
                        // End For Org role migrate
                    }
                }
            });

            DB::commit();
            $this->info('Completed Migration from legacy to learnlab db for ORG-Roles + super-admin');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());
        }
    }
}

// Note : This command should be execute after migrate user and organization tables from legacy to learnlab.
// If user associated only one organization with multiple role in this scenario ,user get role based on higher order.
// If user associated with more then one organization in this scenario ,user get role based on higher order for each organization.
// If user associated with more then one organization with multiple role in this scenario ,user get role based on higher order for each organization.
// Ony need to migrate role ,permission will be automatically assigned ,which is already assigned with role in learnlab.
// Org member management table data should transfer based on above mentioned condition.
// All super admin role users should be transfer on learnlab.

// List below from legacy to learnlab Org level role migrate ( Legacy role name -> Learnlab role name )
// - organisation_manager -> organization_manager
// - org_admin_manager -> organization_owner
// - labmanager -> lab_manager
// - challengemanager -> challenge_manager
// - resourcemanager -> resource_manager
// - user -> user

//  Org Role order list from higher to lower in legacy which is used for ordering
// ['organisation_manager', 'org_admin_manager', 'labmanager', 'challengemanager', 'resourcemanager', 'user']

// New role name in learnlab which is already created
// 'user','super_admin' 'organization_owner', 'organization_manager', 'lab_manager', 'challenge_manager', 'resource_manager','customer_success','developer'
