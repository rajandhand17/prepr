<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\ResourceModule as ResourceModules;
use App\Models\ResourceModuleSkillsGroupsStack;
use DB;
use Illuminate\Console\Command;

class ResourceModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-modules';

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

            $resources = DB::connection('mysql2')->table('resources')->get();
            if ($resources->count() > 0) {
                foreach ($resources as $key => $single_resource) {
                    $check_resource_module = ResourceModules::where('title', $single_resource->res_title)->first();
                  dd($single_resource->resource_skills);
                    if ($check_resource_module) {
                        $newResourceModule = $check_resource_module;
                    } else {
                        $newResourceModule = new ResourceModules();
                    }
                    $newResourceModule->id = $single_resource->id;
                    $newResourceModule->language = $single_resource->language;
                    $newResourceModule->uuid = $single_resource->uuid;
                    $newResourceModule->user_id  = $single_resource->user_id;
                    $newResourceModule->organization_id= $single_resource->org_id;
                    $newResourceModule->title= $single_resource->res_title;
                    $newResourceModule->slug= $single_resource->res_title_slug;
                    $newResourceModule->description= $single_resource->res_desc;
                    $newResourceModule->media_type= $single_resource->res_type;
                    $newResourceModule->media= $single_resource->media_type;
                    $newResourceModule->privacy= $single_resource->media_type;
                    $newResourceModule->status= $single_resource->status;
                    $newResourceModule->is_auto_created= $single_resource->is_auto_created;
                    $newResourceModule->is_global= $single_resource->resourceGlobal;
                    $newResourceModule->save();
                    if($single_resource->resource_skills){
                        foreach ($single_resource->resource_skills as $skill){
                            $newResourceSkills=new ResourceModuleSkillsGroupsStack();
                            $newResourceSkills->resource_module_id=$newResourceModule->id;
                            $newResourceSkills->foreign_id=$skill;
                            $newResourceSkills->type=0;
                            $newResourceSkills->save();
                        }
                    }
                    if($single_resource->skill_groups){
                        foreach($single_resource->skill_groups as $skill_group){
                            $newResourceSkillsGroups=new ResourceModuleSkillsGroupsStack();
                            $newResourceSkillsGroups->resource_module_id=$newResourceModule->id;
                            $newResourceSkillsGroups->foreign_id=$skill_group;
                            $newResourceSkillsGroups->type=1;
                            $newResourceSkillsGroups->save();
                        }
                    }
                    if($single_resource->skill_stacks){
                        foreach($single_resource->skill_stacks as $skill_stacks){
                            $newResourceSkillsStacks=new ResourceModuleSkillsGroupsStack();
                            $newResourceSkillsStacks->resource_module_id=$newResourceModule->id;
                            $newResourceSkillsStacks->foreign_id=$skill_stacks;
                            $newResourceSkillsStacks->type=2;
                            $newResourceSkillsStacks->save();
                        }
                    }

                    if($single_resource->resource_tags){
                        foreach($single_resource->resource_tags as $resource_tags) {
                            $newResourceTags=new ResourceModuleT
                        }
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
