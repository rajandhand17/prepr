<?php

namespace App\Jobs;

use App\Helpers\TrackUserProgressHelper;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\ModuleCompletionStatusService;
use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkUserModuleProgressData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $moduleId;
    protected $moduleType;
    protected $processType;

    public function __construct($moduleId, $moduleType, $processType)
    {
        $this->moduleId = $moduleId;
        $this->moduleType = $moduleType;
        $this->processType = $processType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fetchMemberListEmails = MemberManagementService::getComponentAcceptedMembersBasedOnModuleId($this->moduleId, $this->moduleType);
        if ($fetchMemberListEmails->isNotEmpty()) {
            if ($this->processType === 'delete') {
                switch ($this->moduleType) {
                    case '1':
                        $component = config('constants.module_type.labs');
                        break;

                    case '3':
                        $component = config('constants.module_type.lab_programs');
                        break;
                }

                ModuleCompletionStatusService::deleteUsersProgressBasedOnComponent($this->moduleId, $component);
            } else {
                $fetchUserIdsBasedOnEmailIds = UserService::getUserIdsByEmail($fetchMemberListEmails);
                if ($fetchUserIdsBasedOnEmailIds->isNotEmpty()) {
                    foreach ($fetchUserIdsBasedOnEmailIds as $userId) {
                        switch ($this->moduleType) {
                            case '1':
                                $moduleData = LabService::getLabBasedOnId($this->moduleId);
                                TrackUserProgressHelper::trackLabUserProgress($moduleData, $userId);
                                break;

                            case '3':
                                $moduleData = LabProgramService::getLabProgramBasedOnId($this->moduleId);
                                TrackUserProgressHelper::trackLabProgramUserProgress($moduleData, $userId);
                                break;
                        }
                    }
                }
            }
        }
    }
}
