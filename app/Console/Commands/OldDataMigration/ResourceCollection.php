<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ComponentAssociation;
use App\Models\ResourceCollectionTagsGroups;
use App\Models\ResourceModule as ResourceModules;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\OrganizationService;
use App\Models\Organization;
use App\Models\User;
use App\Services\Manage\ResourceModuleService;
use App\Services\TagService;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use App\Models\ResourceCollection as ResourceCollectionModule;
use DB;
class ResourceCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:resource-collection';

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
        try{
            $this->info('Migrating old data for resource group table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('resourcegroup')->chunkById(1000, function ($resourcesCollection){
                foreach ($resourcesCollection as $singleResourceCollection) {
                    $checkUser=User::find($singleResourceCollection->user_id);
                    if (!$checkUser) {
                        continue;
                    }
                    $organization=Organization::find($singleResourceCollection->org_id);
                    if (!$organization){
                        continue;
                    }
                    $status = config('constants.resource_collection_status.publish');
                    switch ($singleResourceCollection->status) {
                        case 'unlock':
                            $privacy = config('constants.resource_collection_privacy.no');
                            break;
                        case 'lock':
                            $privacy = config('constants.resource_collection_privacy.yes');
                            break;
                        default:
                            $privacy = null;
                    }
                    $checkResourceCollection=ResourceCollectionModule::where('id', $singleResourceCollection->id)->first();
                   if($checkResourceCollection){
                       $resourceCollection=$checkResourceCollection;
                   }else{
                       $resourceCollection = new ResourceCollectionModule;
                   }
                    $resourceCollection->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $resourceCollection->language = $singleResourceCollection->language;
                    $resourceCollection->user_id = $singleResourceCollection->user_id;
                    $resourceCollection->organization_id = $singleResourceCollection->org_id;
                    $resourceCollection->title = $singleResourceCollection->title;
                    $resourceCollection->slug = $singleResourceCollection->slug;
                    $resourceCollection->description = $singleResourceCollection->description;
                    $resourceCollection->media_type = "image";
                    $resourceCollection->media = $singleResourceCollection->image;
                    $resourceCollection->level = "1";
                    $resourceCollection->duration ="1";
                    $resourceCollection->privacy = $privacy;
                    $resourceCollection->status = $status;
                    $resourceCollection->is_accessible = $singleResourceCollection->is_accessable;
                    $resourceCollection->save();

                    /*Add resource collection lab*/
                    if (!empty($singleResourceGroup->assoicated_lab)) {
                        $resourceLabIds=json_decode($singleResourceGroup->assoicated_lab);
                        $getLabId = ChallengeService::getChallengeIdBasedOnId($resourceLabIds);
                        if (!empty($getLabId)) {
                            $existComponentAssociation = ComponentAssociation::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['lab_id', '!=', null],
                            ])->pluck('lab_id')->all();
                            $newComponentAssociation = array_diff($getLabId, $existComponentAssociation);
                            ComponentAssociation::where('resource_collection_id', $singleResourceGroup->id)->whereIn('lab_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['lab_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            $newComponentAssociationId = array_diff($existComponentAssociation, $getLabId);
                            foreach ($newComponentAssociationId as $lab_id) {
                                $sequence++;
                                $challengeAssociation = new ComponentAssociation();
                                $challengeAssociation->resource_collection_id = $singleResourceGroup->id;
                                $challengeAssociation->lab_id = $lab_id;
                                $challengeAssociation->sequence = $sequence;
                                $challengeAssociation->save();
                            }
                        }
                    }
                    /*Add resource group challenge*/
                    if (!empty($singleResourceGroup->assoicated_challange)) {
                        $resourceGroupChallengeIDs=json_decode($singleResourceGroup->assoicated_challange);
                        $getChallengeId = ChallengeService::getChallengeIdBasedOnId($resourceGroupChallengeIDs);
                        if (!empty($getChallengeId)) {
                            $existComponentAssociation = ComponentAssociation::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['challenge_id', '!=', null],
                            ])->pluck('challenge_id')->all();
                            $newComponentAssociation = array_diff($getChallengeId, $existComponentAssociation);
                            ComponentAssociation::where('resource_collection_id', $singleResourceGroup->id)->whereIn('challenge_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['challenge_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            $newComponentAssociationId = array_diff($existComponentAssociation, $getChallengeId);
                            foreach ($newComponentAssociationId as $challenge_id) {
                                $sequence++;
                                $challengeAssociation = new ComponentAssociation();
                                $challengeAssociation->resource_collection_id = $singleResourceGroup->id;
                                $challengeAssociation->challenge_id = $challenge_id;
                                $challengeAssociation->sequence = $sequence;
                                $challengeAssociation->save();
                            }
                        }
                    }

                    /*Add resource module Id*/
                    if (!empty($singleResourceGroup->resource_id)) {
                        $newResourceModuleID=json_decode($singleResourceGroup->resource_id);
                        $getResourceGroupId = ResourceModuleService::getResourceModuleGetBasedId($newResourceModuleID);
                        if (!empty($getResourceGroupId)){
                            $existComponentAssociation = ComponentAssociation::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['resource_module_id', '!=', null],
                            ])->pluck('resource_module_id')->all();
                            $newComponentAssociation = array_diff($getResourceGroupId, $existComponentAssociation);
                            ComponentAssociation::where('resource_collection_id', $singleResourceGroup->id)->whereIn('resource_module_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['resource_module_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            $newResourceId = array_diff($existComponentAssociation, $getResourceGroupId);
                            foreach ($newResourceId as $resource_module_id) {
                                $sequence++;
                                $challengeAssociation = new ComponentAssociation();
                                $challengeAssociation->resource_collection_id = $singleResourceGroup->id;
                                $challengeAssociation->resource_module_id = $resource_module_id;
                                $challengeAssociation->sequence = $sequence;
                                $challengeAssociation->save();
                            }
                        }
                    }
                    /*Add tags*/
                if(!empty($singleResourceGroup->tag)){
                        $resourceCollectionTags=json_decode($singleResourceGroup->tag);
                        $getResourceCollectionTagsId=TagService::getTagsBasedOnIds($resourceCollectionTags);
                        if(!empty($getResourceCollectionTagsId)){
                            $getExistsResourceCollectionTags = ResourceCollectionTagsGroups::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['type', '=', '0'],
                            ])->pluck('foreign_id')->toArray();
                            $nonExistingIds = array_diff($getExistsResourceCollectionTags, $getResourceCollectionTagsId);
                            ResourceCollectionTagsGroups::where([
                                ['resource_collection_id', '=', $singleResourceGroup->id],
                                ['type', '=', '0'],
                            ])->whereIn('foreign_id', $nonExistingIds)->delete();
                            $newTagsCollection = array_diff($getResourceCollectionTagsId, $getExistsResourceCollectionTags);
                            foreach ($newTagsCollection as $tag) {
                                $resourceGroupTag = new ResourceCollectionTagsGroups();
                                $resourceGroupTag->resource_group_id = $singleResourceGroup->id;
                                $resourceGroupTag->foreign_id = $tag;
                                $resourceGroupTag->type = '0';
                                $resourceGroupTag->save();
                            }
                        }
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for resource group table completed.');
            return;

        }catch(\Exception $e){
            DB::rollback();
            $this->error($e);
            return;
        }
    }
}
