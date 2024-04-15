<?php

namespace App\Jobs\ChargeBeeSubscription;

use App\Helpers\ChargeBeeSubscriptionHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubscribePlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;
    protected $organization;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $organization)
    {
        $this->user = $user;
        $this->organization = $organization;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscribePlan = ChargeBeeSubscriptionHelper::subscribePlan($this->user, $this->organization);
    }
}
