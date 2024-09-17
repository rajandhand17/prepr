<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\MemberManagement;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use HiFolks\RandoPhp\Randomize;

class LabMemberManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:lab-members-from-member-management-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to migrate the labs member from legacy member management table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migration of old data for lab members started.');

            // Fetch all lab members
            $allLabMembers = DB::connection('mysql2')->table('member_management')
                ->where('module_type', 'lab')
                ->leftJoin('labs', 'member_management.module_id', '=', 'labs.id')
                ->select('member_management.*')
                ->get();

            if ($allLabMembers->isNotEmpty()) {
                // Pre-fetch invitees, inviters, and labs to minimize DB queries
                $inviteeIds = $allLabMembers->pluck('invitee_id')->toArray();
                $inviterIds = $allLabMembers->pluck('inviter_id')->toArray();
                $moduleIds = $allLabMembers->pluck('module_id')->toArray();

                $invitees = User::whereIn('id', $inviteeIds)->get()->keyBy('id');
                $inviters = User::whereIn('id', $inviterIds)->get()->keyBy('id');
                $labs = Lab::whereIn('id', $moduleIds)->get()->keyBy('id');

                // Cache configurations to avoid repeated config calls
                $inviteTypeConfig = config('constants.member_management_invite_type');
                $inviteStatusConfig = config('constants.member_management_invite_status');
                $autoInviteConfig = config('constants.member_management_auto_invite');
                $emailStatusConfig = config('constants.member_management_email_status');

                // Prepare for batch insertion
                $batchSize = 500;
                $labMembersBatch = [];

                foreach ($allLabMembers as $labMemberData) {
                    // Fetch invitee details
                    $invitee = $invitees->get($labMemberData->invitee_id);
                    $checkInviteeUserEmail = ($invitee) ? $invitee->email : $labMemberData->email;
                    if (!$checkInviteeUserEmail) {
                        continue;
                    }

                    $checkInviteeUserName = UserService::getUserByEmail($checkInviteeUserEmail);

                    // Determine request and data type
                    $requestType = ($labMemberData->is_join_request == '1') ? '1' : '0';
                    $labMemberDataType = ($requestType == '0' && $labMemberData->is_auto_created == '1') ? '2' : '0';

                    // Fetch invite type
                    $labMemberDataInviteType = $inviteTypeConfig[$labMemberData->invite_type] ?? $inviteTypeConfig['email'];

                    // Fetch inviter details, fallback to lab creator if necessary
                    $inviter = $inviters->get($labMemberData->inviter_id);
                    $checkInviterUser = ($inviter) ? $inviter->id : null;
                    if ($checkInviterUser === null) {
                        $findLab = $labs->get($labMemberData->module_id);
                        $checkInviterUser = ($findLab) ? $findLab->user_id : null;
                    }

                    if ($checkInviterUser === null) {
                        continue;
                    }

                    // Determine invite status
                    $labMemberDataInviteStatus = $inviteStatusConfig[$labMemberData->invite_status] ?? $inviteStatusConfig['invited'];

                    // Determine auto invite status
                    $labMemberDataAutoStatus = $autoInviteConfig[$labMemberData->auto_invite_status] ?? $autoInviteConfig['na'];

                    // Determine email status
                    $labMemberDataEmailStatus = $emailStatusConfig[$labMemberData->email_status] ?? $emailStatusConfig['na'];

                    // Check if this entry already exists (prevent duplicates)
                    $existingEntry = MemberManagement::where(['email' => $checkInviteeUserEmail, 'module_id' => $labMemberData->module_id])->exists();

                    if ($existingEntry) {
                        continue; // Skip this entry if it already exists
                    }

                    // Prepare lab member data for batch insertion
                    $labMembersBatch[] = [
                        'uuid' => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                        'type' => $labMemberDataType,
                        'invite_type' => $labMemberDataInviteType,
                        'module_id' => $labMemberData->module_id,
                        'module_type' => config('constants.member_management_component_type.lab'),
                        'inviter_id' => $checkInviterUser,
                        'role' => 'User',
                        'invite_status' => $labMemberDataInviteStatus,
                        'email' => $checkInviteeUserEmail,
                        'auto_invite' => $labMemberDataAutoStatus,
                        'invitee_name' => ($checkInviteeUserName != null) ? $checkInviteeUserName->full_name : null,
                        'email_status' => $labMemberDataEmailStatus,
                        'email_response' => $labMemberData->email_responce,
                        'email_resend_status' => ($labMemberData->email_resend_status == 'yes') ? '1' : '0',
                        'subject_line' => $labMemberData->subject_line,
                        'email_body' => $labMemberData->email_message,
                    ];

                    // Batch insert when we reach the batch size
                    if (count($labMembersBatch) == $batchSize) {
                        MemberManagement::insert($labMembersBatch);
                        $labMembersBatch = [];
                    }
                }

                // Insert remaining batch
                if (!empty($labMembersBatch)) {
                    MemberManagement::insert($labMembersBatch);
                }
            }

            DB::commit();
            $this->info('Migration of old data for lab members completed.');
        } catch (Exception $e) {
            DB::rollBack();
            UtilityHelper::logError($e);
            $this->error($e->getMessage());
            return false;
        }
    }
}
