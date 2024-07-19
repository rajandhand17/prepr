<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Models\ResourceCollection as ResourceCollectionModule;
use App\Models\ResourceCollectionTagsGroups;
use App\Models\User;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Console\Command;

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
        try {
            $this->info('Migrating old data for resource collection table.');
            DB::beginTransaction();
            DB::connection('mysql2')->table('resourcegroup')->chunkById(1000, function ($resourcesCollection) {
                foreach ($resourcesCollection as $singleResourceCollection) {
                    $checkUser = User::find($singleResourceCollection->user_id);
                    if (!$checkUser) {
                        continue;
                    }
                    $organization = Organization::find($singleResourceCollection->org_id);
                    if (!$organization) {
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
                    $checkResourceCollection = ResourceCollectionModule::where('id', $singleResourceCollection->id)->first();
                    if ($checkResourceCollection) {
                        $resourceCollection = $checkResourceCollection;
                    } else {
                        $resourceCollection = new ResourceCollectionModule();
                    }
                    $resourceCollection->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
                    $resourceCollection->language = $singleResourceCollection->language;
                    $resourceCollection->user_id = $singleResourceCollection->user_id;
                    $resourceCollection->organization_id = $singleResourceCollection->org_id;
                    $resourceCollection->title = $singleResourceCollection->title;
                    $resourceCollection->slug = $singleResourceCollection->slug;
                    $resourceCollection->description = $singleResourceCollection->description;
                    $resourceCollection->media_type = 'image';
                    $resourceCollection->media = $singleResourceCollection->image;
                    $resourceCollection->level = '1';
                    $resourceCollection->duration = '1';
                    $resourceCollection->privacy = $privacy;
                    $resourceCollection->status = $status;
                    $resourceCollection->is_accessible = $singleResourceCollection->is_accessable;
                    $resourceCollection->save();

                    /*Add tags*/
                    $resourceCollectionTags = json_decode($singleResourceCollection->tag, true);
                    if ($resourceCollectionTags) {
                        ResourceCollectionTagsGroups::where(['resource_collection_id' => $singleResourceCollection->id, 'foreign_id' => '0'])->delete();
                        foreach (array_filter($resourceCollectionTags) as $tag) {
                            $resourceGroupTag = new ResourceCollectionTagsGroups();
                            $resourceGroupTag->resource_collection_id = $singleResourceCollection->id;
                            $resourceGroupTag->foreign_id = $tag;
                            $resourceGroupTag->type = '0';
                            $resourceGroupTag->save();
                        }
                    }
                }
            });
            DB::commit();
            $this->info('Migrating of old data for resource Collection table completed.');

            return;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e);

            return;
        }
    }
}
