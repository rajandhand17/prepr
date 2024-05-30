<?php

namespace App\Jobs\UserAchievement;

use App\Services\Manage\ChallengePathService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessChallengePathAchievementJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $fetchedMemberIds;
    protected $challengeId;

    /**
     * Create a new job instance.
     */
    public function __construct($fetchedMemberIds, $challengeId)
    {
        $this->fetchedMemberIds = $fetchedMemberIds;
        $this->challengeId = $challengeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $challengePathAchievementStatus = ChallengePathService::challengePathAchievementStatus($this->fetchedMemberIds, $this->challengeId);
    }
}
