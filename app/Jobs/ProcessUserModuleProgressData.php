<?php

namespace App\Jobs;

use App\Helpers\TrackUserProgressHelper;
use App\Services\Manage\LabProgramService;
use App\Services\Manage\LabService;
use App\Services\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserModuleProgressData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $userEmail;
    protected $moduleId;
    protected $moduleType;

    public function __construct($userEmail, $moduleId, $moduleType)
    {
        $this->userEmail = $userEmail;
        $this->moduleId = $moduleId;
        $this->moduleType = $moduleType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userId = UserService::getUserByEmail($this->userEmail)->id;
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
