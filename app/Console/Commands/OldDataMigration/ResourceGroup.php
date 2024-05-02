<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ComponentAssociation;
use App\Models\Organization;
use App\Models\ResourceGroup as ResourceGroupModel;
use App\Models\ResourceGroupAchievement;
use App\Models\User;
use App\Services\Manage\ResourceCollectionService;
use App\Services\Manage\ResourceModuleService;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResourceGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Migrating old data for resource group table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('groups')->where('type', 'resource')->chunkById(1000, function ($resourcesGroup) {
                foreach ($resourcesGroup as $singleResourceGroup) {
                    $checkUser = User::find($singleResourceGroup->user_id);
                    if (!$checkUser) {
                        continue;
                    }
                    $organization = Organization::find($singleResourceGroup->organisation);
                    if (!$organization) {
                        continue;
                    }
                    switch($singleResourceGroup->status) {
                        case 'open':
                            $status = config('constants.resource_module_status.publish');
                            break;
                        case 'closed':
                            $status = config('constants.resource_module_status.draft');
                            break;
                        default:
                            $status = config('constants.resource_module_status.draft');
                            break;
                    }
                    switch ($singleResourceGroup->privacy_project) {
                        case 'public':
                            $privacy = config('constants.resource_group_privacy.no');
                            break;
                        case 'private':
                            $privacy = config('constants.resource_group_privacy.yes');
                            break;
                        default:
                            $privacy = null;
                    }
                    $checkResourceGroup = ResourceGroupModel::where('id', $singleResourceGroup->id)->first();
                    if ($checkResourceGroup) {
                        $newResourceGroup = $checkResourceGroup;
                    } else {
                        $newResourceGroup = new ResourceGroupModel();
                    }
                    $slug = UtilityHelper::generateSlug($singleResourceGroup->title, $newResourceGroup);
                    $newResourceGroup->id = $singleResourceGroup->id;
                    $newResourceGroup->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newResourceGroup->language = $singleResourceGroup->language;
                    $newResourceGroup->user_id = $singleResourceGroup->user_id;
                    $newResourceGroup->organization_id = $singleResourceGroup->organisation;
                    $newResourceGroup->title = $singleResourceGroup->title;
                    $newResourceGroup->slug = $slug;
                    $newResourceGroup->description = $singleResourceGroup->description;
                    $newResourceGroup->media_type = 'image';
                    $newResourceGroup->media = $singleResourceGroup->group_image;
                    $newResourceGroup->level = '1';
                    $newResourceGroup->duration = '1';
                    $newResourceGroup->privacy = $privacy;
                    $newResourceGroup->status = $status;
                    $newResourceGroup->is_accessible = $singleResourceGroup->is_accessable;
                    $newResourceGroup->save();

                    /*Add resource module Id*/
                    if (!empty($singleResourceGroup->resource_id)) {
                        $newResourceModuleID = explode(',', $singleResourceGroup->resource_id);
                        $getResourceGroupId = ResourceModuleService::getResourceModuleGetBasedId($newResourceModuleID);
                        if (!empty($getResourceGroupId)) {
                            $existComponentAssociation = ComponentAssociation::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['resource_module_id', '!=', null],
                            ])->pluck('resource_module_id')->all();
                            $newComponentAssociation = array_diff($getResourceGroupId, $existComponentAssociation);
                            ComponentAssociation::where('resource_group_id', $singleResourceGroup->id)->whereIn('resource_module_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['resource_module_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            $newResourceId = array_diff($existComponentAssociation, $getResourceGroupId);
                            foreach ($newResourceId as $resource_module_id) {
                                $sequence++;
                                $challengeAssociation = new ComponentAssociation();
                                $challengeAssociation->resource_group_id = $singleResourceGroup->id;
                                $challengeAssociation->resource_module_id = $resource_module_id;
                                $challengeAssociation->sequence = $sequence;
                                $challengeAssociation->save();
                            }
                        }
                    }

                    /*Add resource collection Id*/
                    if (!empty($singleResourceGroup->collection_id)) {
                        $newResourceCollectionID = explode(',', $singleResourceGroup->collection_id);
                        $getResourceGroupCollectionId = ResourceCollectionService::getResourceCollectionGetBasedId($newResourceCollectionID);
                        if (!empty($getResourceGroupCollectionId)) {
                            $existComponentAssociation = ComponentAssociation::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['resource_collection_id', '!=', null],
                            ])->pluck('resource_collection_id')->all();
                            $newComponentAssociation = array_diff($getResourceGroupCollectionId, $existComponentAssociation);
                            ComponentAssociation::where('resource_group_id', $singleResourceGroup->id)->whereIn('resource_collection_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['resource_collection_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            $newResourceId = array_diff($existComponentAssociation, $getResourceGroupCollectionId);
                            foreach ($newResourceId as $resource_collection_id) {
                                $sequence++;
                                $challengeAssociation = new ComponentAssociation();
                                $challengeAssociation->resource_group_id = $singleResourceGroup->id;
                                $challengeAssociation->resource_collection_id = $resource_collection_id;
                                $challengeAssociation->sequence = $sequence;
                                $challengeAssociation->save();
                            }
                        }
                    }
                    /*Add resource achievement*/
                    if (!empty($singleResourceGroup->prize)) {
                        $resourceGroupAchievement = ResourceGroupAchievement::firstOrNew(['resource_group_id' => $singleResourceGroup->id]);
                        $resourceGroupAchievement->achievement_name = (string) $singleResourceGroup->prize;
                        $resourceGroupAchievement->achievement_points = $singleResourceGroup->points;
                        $resourceGroupAchievement->achievement_image = $singleResourceGroup->trophy;
                        $resourceGroupAchievement->save();
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for resource group table completed.');

            return;
        } catch(\Exception $e) {
            DB::rollback();
            $this->error($e);

            return;
        }
    }
}
