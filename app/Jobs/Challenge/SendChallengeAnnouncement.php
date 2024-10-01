<?php

namespace App\Jobs\Challenge;

use App\Services\Manage\ChallengeAnnouncementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendChallengeAnnouncement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $challengeAnnouncementId;

    /**
     * Create a new job instance.
     */
    public function __construct($challengeAnnouncementId)
    {
        $this->challengeAnnouncementId = $challengeAnnouncementId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $challengeAnnouncement = new ChallengeAnnouncementService();
        $challengeAnnouncementStatus = $challengeAnnouncement->sendChallengeAnnouncement($this->challengeAnnouncementId);
    }
}
