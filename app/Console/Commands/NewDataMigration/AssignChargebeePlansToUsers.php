<?php

namespace App\Console\Commands\NewDataMigration;

use App\Models\User;
use Illuminate\Console\Command;
use DB;
use PhpParser\Builder\Class_;
use App\Helpers\PlanSubscriptionHelper;
use App\Models\Organization;

class AssignChargebeePlansToUsers extends Command{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-new-data:users-assigned-roles-chargebee';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old tag table data to new db structure.';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
     /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for tags table.');
            DB::beginTransaction();
            $all_organizations=Organization::get();
            foreach ($all_organizations as $key => $org){
                $user=User::where("id",$org->user_id)->first();
                if($user){
                  if($user->hasRole(['free_organisation_manager'],$org->id)){
                      $user->detachRole("free_organisation_manager",$org->id);
                      $user->attachRole("organization_manager",$org->id);
                      $userCreated = PlanSubscriptionHelper::createCustomer($user,$user->preferred_language);
                      $planSubscribed  = PlanSubscriptionHelper::freePlanSubscribe($userCreated);
                    }   
                }
            }
            DB::commit();
            $this->info('Run the command for assign role and move on chargebee');
            return;
        }catch(\Exception $e){
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}