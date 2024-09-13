<?php

namespace App\Jobs;

use App\Helpers\MixpanelHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MixpanelJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $event;
    protected $profileData;
    protected $user;
    protected $ip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($event, $profileData, $user, $ip)
    {
        $this->event = $event;
        $this->profileData = $profileData;
        $this->user = $user;
        $this->ip = $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        // Call the helper method with the stored data
        MixpanelHelper::mixpanel_tracking($this->event, $this->profileData, $this->user, $this->ip);
    }
}
