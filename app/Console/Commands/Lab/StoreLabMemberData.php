<?php

namespace App\Console\Commands\Lab;

use App\Helpers\TrackUserProgressHelper;
use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;
use App\Services\Manage\LabService;
use App\Services\UserService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StoreLabMemberData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lab-progress-status:update-existing-users-progress-module_completion_statuses-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for updated for the existing users and add in module completion statuses table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Command Started for lab members progress...');
            DB::beginTransaction();

            $moduleType = '1';
            $invite_status = '1';
            $getModuleDatas = MemberManagement::where(['module_type' => $moduleType, 'invite_status' => $invite_status])->join('labs', 'member_management.module_id', '=', 'labs.id')->join('users', 'member_management.email', '=', 'users.email')->get();

            if ($getModuleDatas->isNotEmpty()) {
                foreach ($getModuleDatas as $getModule) {
                    $fetchUser = UserService::getUserByEmail($getModule->email);
                    $fetchLab = LabService::getLabBasedOnId($getModule->module_id);
                    if ($fetchLab && $fetchUser) {
                        TrackUserProgressHelper::trackLabUserProgress($fetchLab, $fetchUser->id);
                    }
                }
            }

            $this->info('Completed to check users progress in lab');

            DB::commit();
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();
            return false;
        }
    }
}
