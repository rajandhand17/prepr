<?php

namespace App\Jobs\Chargebee;

use App\Helpers\ChargebeeHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SubscribePlanJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $user;
    protected $organization;
    protected $planDetail;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $organization, $planDetail)
    {
        $this->user = $user;
        $this->organization = $organization;
        $this->planDetail = $planDetail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customerDetails = ChargebeeHelper::getCustomer($this->user->email);
        $subscribePlan = ChargebeeHelper::subscribePlan($customerDetails, $this->organization, $this->planDetail);
    }
}
