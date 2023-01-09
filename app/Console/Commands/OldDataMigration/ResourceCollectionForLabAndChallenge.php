<?php

namespace App\Console\Commands\OldDataMigration;

use Illuminate\Console\Command;
use DB;
use App\Models\ResourceCollectionForLabAndChallenge as ResourceCollectionForLabAndChallenges;

class ResourceCollectionForLabAndChallenge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-collection-for-lab-and-challenge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old resource collection for lab and challenge data to new db structure.';

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

            $this->info('Migrating old data for resource collection for lab and challenge table.');
            DB::beginTransaction();

            $resource_collection_for_lab_and_challenges = DB::connection('mysql2')->table('resource_collection_for_lab_and_challenges')->get();
            if($resource_collection_for_lab_and_challenges->count() > 0){
                
                foreach ($resource_collection_for_lab_and_challenges as $key => $single_resource_collection_for_lab_and_challenges){
                   $resource_collection_details=[
                        'user_id' => $single_resource_collection_for_lab_and_challenges->user_id,
                        'lab_id' => $single_resource_collection_for_lab_and_challenges->lab_id,
                        'challenge_id' => $single_resource_collection_for_lab_and_challenges->challenge_id,
                        'resource_collection_id' => $single_resource_collection_for_lab_and_challenges->resource_collection_id,
                    ];
                    $check_resource_collection = ResourceCollectionForLabAndChallenges::where($resource_collection_details)->first();
                    if(!$check_resource_collection){
                        ResourceCollectionForLabAndChallenges::create($resource_collection_details);
                    }
                  
                }
                DB::commit();
                $this->info('Migrating of old data for resource collection for lab and challenges table completed.');
                return;
            }
            DB::rollback();
            $this->error('No resource collection for lab and challenges found.');

        } catch (\Exception $e) {
           DB::rollback();
            $this->error('Something went wrong.');
            return;
        }
    }
}
