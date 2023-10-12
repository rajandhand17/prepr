<?php

namespace App\Console\Commands\OldDataMigration;

use App\Models\ResourceModule;
use App\Models\ResourceModuleDetail;
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
            $this->info('Migrating old data for resource module details table.');
            DB::beginTransaction();
            $resourceDetails = DB::connection('mysql2')->table('resource_module_details')->get();
            if ($resourceDetails->count() > 0) {
                foreach ($resourceDetails as  $single_resource_module_details){
                    $resourceModule=ResourceModule::where("id",$single_resource_module_details->resource_id)->first();
                    if($resourceModule==null){
                        continue;
                    }
                    switch($single_resource_module_details->type){
                        case 'header':
                            $type='header';
                            break;
                        case 'document':
                            $type =config('constants.resource_module_type.document');
                            break;
                        case 'video':
                            $type =config('constants.resource_module_type.video');
                            break;
                        case 'audio':
                            $type =config('constants.resource_module_type.audio');
                            break;
                        case 'embedded':
                            $type =config('constants.resource_module_type.embedded_video');
                            break;
                        case 'embedded_audio':
                            $type =config('constants.resource_module_type.embedded_audio');
                            break;
                        case 'url':
                            $type =config('constants.resource_module_type.url');
                            break;
                        case 'image':
                            $type =config('constants.resource_module_type.image');
                            break;
                        case 'Embedded_Cover_Video':
                            $type =config('constants.resource_module_type.Embedded_Cover_Video');
                            break;
                        default:
                            $type=null;
                            break;
                    }
                    dd($type);
                    if($type=="header"){
                        continue;
                    }else{
                        $this->info('start data inserting');
                    }
                    $this->info('start data inserting');
                    $newResourceModule=new ResourceModuleDetail();
                    $newResourceModule->resource_module_id=$single_resource_module_details->resource_id;
                    $newResourceModule->title=$single_resource_module_details->title;
                    $newResourceModule->type=$type;
                    $newResourceModule->path=$single_resource_module_details->path;
                    $newResourceModule->social_link_id=$single_resource_module_details->social_link_id;
                    $newResourceModule->save();

                }
                DB::commit();
                $this->info('Migrating of old data for resource module details table completed.');
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
