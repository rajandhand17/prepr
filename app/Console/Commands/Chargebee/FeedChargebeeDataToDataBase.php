<?php

namespace App\Console\Commands\Chargebee;

use App\Helpers\ChargebeeHelper;
use App\Models\ChargebeeSubscription;
use App\Models\Organization;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FeedChargebeeDataToDataBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:feed-chargebee-data-to-data-base';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used to feed chargebee details based on organization into the local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating of old data for Chargebee Organization table started.');
            DB::beginTransaction();
            $fetchOrganizations = Organization::orderBy('id', 'ASC')->get();
            foreach ($fetchOrganizations as $organization) {
                $checkChargebeeDetail = ChargebeeSubscription::where('organization_id', $organization->id)->first();
                if (!$checkChargebeeDetail) {
                    $organizationData = ChargebeeHelper::createChargebeePlanDetails($organization->id);
                }
            }
            DB::commit();
            $this->info('Migrating of old data for Chargebee Organization table completed.');

            return;
        } catch (Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
