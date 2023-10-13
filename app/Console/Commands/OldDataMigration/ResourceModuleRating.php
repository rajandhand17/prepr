<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\ResourceModule;
use App\Models\ResourceModuleRating as ResourceModuleRatings;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use App\Models\SkillGroup;

class ResourceModuleRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-modules-rating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old resource module rating table data to new db structure.';

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
            $this->info('Migrating old data for resource module rating table.');
            DB::beginTransaction();
            $resourceRating = DB::connection('mysql2')->table('user_resource_ratings')->get();
            if ($resourceRating->count() > 0) {
                foreach ($resourceRating as  $single_resource_module_rating){
                    $resourceModule=ResourceModule::where("id",$single_resource_module_rating->res_id)->first();
                    if($resourceModule==null){
                        continue;
                    }
                    $newResourceModuleRating=new ResourceModuleRatings();
                    $newResourceModuleRating->resource_module_id =$single_resource_module_rating->res_id;
                    $newResourceModuleRating->user_id =$single_resource_module_rating->user_id;
                    $newResourceModuleRating->rating =$single_resource_module_rating->ratting;
                    $newResourceModuleRating->save();

                }
                DB::commit();
                $this->info('Migrating of old data for resource module rating table completed.');
                return;
            }
            DB::rollback();
            $this->error('No resource module rating found.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
