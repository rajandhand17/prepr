<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePath as ModelsChallengePath;
use App\Models\ComponentAssociation;
use App\Models\Organization;
use App\Models\ResourceGroupTagGroups;
use App\Models\User;
use App\Services\Manage\ChallengeService;
use App\Services\Manage\ResourceGroupTagsGroupsService;
use App\Services\Manage\ResourceModuleService;
use App\Services\TagService;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ResourceGroup as ModelResourceGroup;
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
        try{
            $this->info('Migrating old data for resource group table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('groups')->where("type","resource")->chunkById(1000, function ($resourcesGroup){
               foreach ($resourcesGroup as $singleResourceGroup){
                    $checkUser = User::find($singleResourceGroup->user_id);
                    if (!$checkUser) {
                        continue;
                    }
                    $organization = Organization::find($singleResourceGroup->org_id);
                    if (!$organization) {
                        continue;
                    }
                    $status = config('constants.resource_group_status.publish');
                    switch ($singleResourceGroup->status){
                        case 'unlock':
                            $privacy = config('constants.resource_group_privacy.no');
                            break;
                        case 'lock':
                            $privacy = config('constants.resource_group_privacy.yes');
                            break;
                        default:
                            $privacy = null;
                    }
                    $checkResourceGroup = ModelResourceGroup::where('id', $singleResourceGroup->id)->first();
                    if ($checkResourceGroup) {
                        $newResourceGroup = $checkResourceGroup;
                    } else {
                        $newResourceGroup = new ModelResourceGroup();
                    }
                    $newResourceGroup->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $newResourceGroup->language = $singleResourceGroup->language;
                    $newResourceGroup->user_id = $singleResourceGroup->user_id;
                    $newResourceGroup->organization_id = $singleResourceGroup->org_id;
                    $newResourceGroup->title = $singleResourceGroup->title;
                    $newResourceGroup->slug = $singleResourceGroup->slug;
                    $newResourceGroup->description = $singleResourceGroup->description;
                    $newResourceGroup->media_type = 'image';
                    $newResourceGroup->media = $singleResourceGroup->image;
                    $newResourceGroup->level = '1';
                    $newResourceGroup->duration ='1';
                    $newResourceGroup->privacy = $privacy;
                    $newResourceGroup->status = $status;
                    $newResourceGroup->save();

                    /*Add resource group challenge*/
                    if (!empty($singleResourceGroup->assoicated_challange)) {
                        $resourceGroupChallengeIDs=json_decode($singleResourceGroup->assoicated_challange);
                        $getChallengeId = ChallengeService::getChallengeIdBasedOnId($resourceGroupChallengeIDs);
                        if (!empty($getChallengeId)) {
                            $existComponentAssociation = ComponentAssociation::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['challenge_id', '!=', null],
                            ])->pluck('challenge_id')->all();
                            $newComponentAssociation = array_diff($getChallengeId, $existComponentAssociation);
                            ComponentAssociation::where('resource_group_id', $singleResourceGroup->id)->whereIn('challenge_id', $newComponentAssociation)->delete();
                            $sequence = ComponentAssociation::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['challenge_id', '!=', null],
                            ])->select('sequence')->orderBy('id', 'desc')->first();
                            $newComponentAssociationId = array_diff($existComponentAssociation, $getChallengeId);
                            foreach ($newComponentAssociationId as $challenge_id) {
                                $sequence++;
                                $challengeAssociation = new ComponentAssociation();
                                $challengeAssociation->resource_group_id = $singleResourceGroup->id;
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
                    /*Add tags*/
                    if(!empty($singleResourceGroup->tag)){
                        $resourceGroupTags=json_decode($singleResourceGroup->tag);
                        $getResourceGroupTagsId=TagService::getTagsBasedOnIds($resourceGroupTags);
                        if(!empty($getResourceGroupTagsId)){
                            $getExistsGroupCollectionTags = ResourceGroupTagGroups::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['type', '=', '0'],
                            ])->pluck('foreign_id')->toArray();
                            $nonExistingIds = array_diff($getExistsGroupCollectionTags, $getResourceGroupTagsId);
                            $deleteNonExisting = ResourceGroupTagGroups::where([
                                ['resource_group_id', '=', $singleResourceGroup->id],
                                ['type', '=', '0'],
                            ])->whereIn('foreign_id', $nonExistingIds)->delete();
                            $newTagsGroups = array_diff($getResourceGroupTagsId, $getExistsGroupCollectionTags);
                            foreach ($newTagsGroups as $tag) {
                                $resourceGroupTag = new ResourceGroupTagGroups();
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
