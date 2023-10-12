<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Organization;
use App\Models\ResourceModuleDetail;
use App\Models\Skill;
use App\Models\User;
use App\Models\Tag;
use App\Models\SkillStack;
use App\Models\ResourceModule as ResourceModules;
use App\Models\ResourceModuleSkillsGroupsStack;
use App\Models\ResourceModuleTagsGroups;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use App\Models\SkillGroup;

class ResourceModuleDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-modules-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is use to migrate old resource module table data to new db structure.';

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
            $this->info('Migrating old data for resource module table.');
            DB::beginTransaction();
            $resourceDetails = DB::connection('mysql2')->table('resource_module_details')->first();

            if ($resourceDetails->count() > 0) {
                foreach ($resourceDetails as  $single_resource_module_details){

                    $check_resource_module = ResourceModules::where('title', $single_resource->res_title)->first();

                    if ($check_resource_module) {
                        $newResourceModule = $check_resource_module;
                    } else {
                        $newResourceModule = new ResourceModuleDetail();
                    }


                }
                DB::commit();
                $this->info('Migrating of old data for resource module table completed.');
                return;
            }
            DB::rollback();
            $this->error('No resource module found.');
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
            return;
        }
    }
}
