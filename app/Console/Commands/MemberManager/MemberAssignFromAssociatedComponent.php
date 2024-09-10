<?php

namespace App\Console\Commands\MemberManager;

use App\Helpers\UtilityHelper;
use App\Models\ComponentAssociation;
use App\Models\MemberManagement;
use App\Models\User;
use App\Services\Manage\MemberManagementService;
use DB;
use Illuminate\Console\Command;

class MemberAssignFromAssociatedComponent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member-management:member-assign-from-associated-component';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use for adding automatic member from associated component.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $members = MemberManagement::select('id', 'module_type', 'inviter_id', 'module_id', 'email', 'invitee_name')->where(['module_type' => '1', 'invite_status' => '1'])->get();
            if (!empty($members)) {
                foreach ($members as $member) {
                    if (!empty($member->email)) {
                        $inviteeData = User::where('email', $member->email)->first();
                        if (!empty($inviteeData)) {
                            $challengeIds = ComponentAssociation::where('lab_id', $member->module_id)->whereNotNull('challenge_id')->pluck('challenge_id');
                            if (!empty($challengeIds)) {
                                foreach ($challengeIds as $challengeId) {
                                    MemberManagementService::autoAssignedMemberFromAssociatedComponent(['type' => '1', 'invite_type' => '1', 'module_id' => $challengeId, 'module_type' => '2', 'inviter_id' => $member->inviter_id, 'role' => 'user', 'invite_status' => '1', 'email' => $member->email, 'auto_invite' => '2', 'invitee_name' => $inviteeData->full_name, 'email_status' => '3', 'email_resend_status' => '0', 'subject_line' => null, 'email_body' => null, 'is_associated_member' => 'yes', 'associated_component' => 'lab', 'associated_component_id' => $member->module_id]);
                                    DB::rollback();
                                }
                            }
                        }
                    }
                }
            }
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error('Member manger emails not send');
        }
    }
}
