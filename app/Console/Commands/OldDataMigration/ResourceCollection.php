<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Models\ResourceCollection as ResourceCollectionModule;
use App\Models\ResourceCollectionTagsGroups;
use App\Models\ResourceCollectionTypeModes;
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

                    $getTagGroups = DB::connection('mysql2')->table('manage_tag_group')->where(['module_id' => $singleResourceCollection->id, 'module_type' => 'resource_collection']);
                    // Clone the query to avoid modifying the original
                    $getDuration = clone $getTagGroups;
                    $duration = $getDuration->where('group_type', 'duration')->pluck('group_tag_id')->first();
                    $duration_id = null;
                    if ($duration) {
                        if ($duration == '["169"]') {
                            $duration_id = '1';
                        } elseif ($duration == '["170"]') {
                            $duration_id = '2';
                        } elseif ($duration == '["171"]') {
                            $duration_id = '3';
                        } elseif ($duration == '["172"]') {
                            $duration_id = '4';
                        } elseif ($duration == '["173"]') {
                            $duration_id = '5';
                        } elseif ($duration == '["174"]') {
                            $duration_id = '6';
                        }
                    }

                    $getLevel = clone $getTagGroups;
                    $level = $getLevel->where('group_type', 'level')->pluck('group_tag_id')->first();
                    $level_id = null;
                    if ($level) {
                        if ($level == '["157"]') {
                            $level_id = '1';
                        } elseif ($level == '["158"]') {
                            $level_id = '2';
                        } elseif ($level == '["159"]') {
                            $level_id = '3';
                        } elseif ($level == '["160"]') {
                            $level_id = '4';
                        }
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
                    $resourceCollection->media_type = '0';  //0 for image and 1 for embedded
                    $resourceCollection->media = $singleResourceCollection->image;
                    $resourceCollection->level =  $level_id;
                    $resourceCollection->duration = $duration_id;
                    $resourceCollection->privacy = $privacy;
                    $resourceCollection->status = $status;
                    $resourceCollection->is_accessible = $singleResourceCollection->is_accessable;
                    $resourceCollection->save();

                    
                    //for mode and type
                    $getMode = clone $getTagGroups;
                    $mode = $getMode->where('group_type', 'mode')->pluck('group_tag_id')->first();
                    if ($mode) {
                        $modes = json_decode($mode, true);
                        if (!empty($modes)) {
                            ResourceCollectionTypeModes::where(['resource_collection_id' => $singleResourceCollection->id, 'type_mode' => '1'])->delete();
                            foreach ($modes as $single_mode) {
                                if ($single_mode == '196') {
                                    $mode_id = '4';
                                } elseif ($single_mode == '197') {
                                    $mode_id = '5';
                                }
                                $resourceCollectionMode = new ResourceCollectionTypeModes();
                                $resourceCollectionMode->resource_collection_id = $singleResourceCollection->id;
                                $resourceCollectionMode->type_mode = '1';
                                $resourceCollectionMode->value = $mode_id;
                                $resourceCollectionMode->save();
                            }
                        }
                    }

                    $getType = clone $getTagGroups;
                    $type = $getType->where('group_type', 'type')->pluck('group_tag_id')->first();
                    if ($type) {
                        $types = json_decode($type, true);
                        if (!empty($types)) {
                            ResourceCollectionTypeModes::where(['resource_collection_id' => $singleResourceCollection->id, 'type_mode' => '0'])->delete();
                            foreach ($types as $single_type) {
                                if ($single_type == '192') {
                                    $type_id = '0';
                                } elseif ($single_type == '193') {
                                    $type_id = '1';
                                } elseif ($single_type == '194') {
                                    $type_id = '2';
                                } elseif ($single_type == '195') {
                                    $type_id = '3';
                                }
                                $resourceCollectionType = new ResourceCollectionTypeModes();
                                $resourceCollectionType->resource_collection_id = $singleResourceCollection->id;
                                $resourceCollectionType->type_mode = '0';
                                $resourceCollectionType->value = $type_id;
                                $resourceCollectionType->save();
                            }
                        }
                    }

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
