<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\Organization;
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
                foreach ($resources as  $single_resource) {
                    $checkUser = User::find($single_resource->user_id);
                    if (!$checkUser) {
                        continue;
                    }
                    $organization=Organization::find($single_resource->org_id);
                    if(!$organization){
                        continue;
                    }
                    $check_resource_module = ResourceModules::where('title', $single_resource->res_title)->first();

                    $resourceDetails = DB::connection('mysql2')->table('resource_module_details')->where(['type'=>'header','resource_id'=>$single_resource->id])->first();
                    if($resourceDetails!==null){
                       $media=$resourceDetails->path;
                    }else{
                        $media = config('site-settings.default_resource_module_cover_image');
                    }

                    switch($single_resource->status){
                        case 'open':
                            $privacy =config('constants.resource_module_privacy.no');
                            break;
                        case 'closed':
                            $privacy =config('constants.resource_module_privacy.yes');
                            break;
                        default:
                            $privacy = config('constants.resource_module_privacy.yes');
                            break;
                    }
                    $status = config('constants.resource_module_status.publish');
                    if ($check_resource_module) {
                        $newResourceModule = $check_resource_module;
                    } else {
                        $newResourceModule = new ResourceModules();
                    }
                    $newResourceModule->id = $single_resource->id;
                    $newResourceModule->language = $single_resource->language;
                    $newResourceModule->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newResourceModule->user_id  = $single_resource->user_id;
                    $newResourceModule->organization_id= $single_resource->org_id;
                    $newResourceModule->title= $single_resource->res_title;
                    $newResourceModule->slug= $single_resource->res_title_slug;
                    $newResourceModule->description= $single_resource->res_desc;
                    $newResourceModule->media= $media;
                    $newResourceModule->privacy= $privacy;
                    $newResourceModule->status=$status;
                    $newResourceModule->is_global= $single_resource->resourceGlobal;
                    $newResourceModule->save();

                    if ($single_resource->resource_skills) {
                        $skillIds = json_decode($single_resource->resource_skills);

                        if (!empty($skillIds)) {
                            $existingSkills = Skill::whereIn('id', $skillIds)->get();

                            foreach ($existingSkills as $skill) {
                                $newResourceSkills = new ResourceModuleSkillsGroupsStack();
                                $newResourceSkills->resource_module_id = $newResourceModule->id;
                                $newResourceSkills->foreign_id = $skill->id;
                                $newResourceSkills->type ='0';
                                $newResourceSkills->save();
                            }
                        }
                    }
                    if ($single_resource->skill_groups) {
                        $skillGroupsIds = json_decode($single_resource->skill_groups);

                        if (!empty($skillGroupsIds) && is_array($skillGroupsIds)){
                            $existingSkillGroups = SkillGroup::whereIn('id', $skillGroupsIds)->get();

                            foreach ($existingSkillGroups as $skillGroup) {
                                $newResourceSkillsGroups = new ResourceModuleSkillsGroupsStack();
                                $newResourceSkillsGroups->resource_module_id = $newResourceModule->id;
                                $newResourceSkillsGroups->foreign_id = $skillGroup->id;
                                $newResourceSkillsGroups->type = '1'; // Assuming "type" is an integer in the database.
                                $newResourceSkillsGroups->save();
                            }
                        }
                    }

                    if ($single_resource->skill_stacks) {
                        $skillStacksIds = json_decode($single_resource->skill_stacks);
                        if(!empty($skillStacksIds) && is_array($skillStacksIds)){
                            foreach ($skillStacksIds as $skillStackId) {
                                $skillStack = SkillStack::find($skillStackId);

                                if ($skillStack) {
                                    $newResourceSkillsStacks = new ResourceModuleSkillsGroupsStack([
                                        'resource_module_id' => $newResourceModule->id,
                                        'foreign_id' => $skillStack->id,
                                        'type' => '2', // Assuming "type" is an integer in the database.
                                    ]);
                                    $newResourceSkillsStacks->save();
                                }
                            }
                        }
                    }

                    if ($single_resource->resource_tags) {
                        $tagIds = json_decode($single_resource->resource_tags);
                        if(!empty($tagIds) && is_array($tagIds)){
                            foreach ($tagIds as $tagId) {
                                if ($tag = Tag::find($tagId)) {
                                    ResourceModuleTagsGroups::create([
                                        'resource_module_id' => $newResourceModule->id,
                                        'foreign_id' => $tag->id,
                                        'type' => '0', // Assuming "type" is an integer in the database.
                                    ]);
                                }
                            }
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
