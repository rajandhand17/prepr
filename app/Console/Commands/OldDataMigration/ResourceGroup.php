<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Models\ResourceGroup as ResourceGroupModel;
use App\Models\ResourceGroupAchievement;
use App\Models\ResourceGroupTypeModes;
use App\Models\User;
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
                    switch ($singleResourceGroup->is_auto_created) {
                        case '0':
                            $is_auto_created_resourceGroup = '0';
                            break;
                        case '1':
                            $is_auto_created_resourceGroup = '1';
                            break;
                        default:
                            $is_auto_created_resourceGroup = '0';
                            break;
                    }

                    $getTagGroups = DB::connection('mysql2')->table('manage_tag_group')->where(['module_id' => $singleResourceGroup->id, 'module_type' => 'resource_group']);
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
                    $newResourceGroup->media_type = '0';  //0 for image and 1 for embedded
                    $newResourceGroup->media = $singleResourceGroup->group_image;
                    $newResourceGroup->level = $level_id;
                    $newResourceGroup->duration = $duration_id;
                    $newResourceGroup->privacy = $privacy;
                    $newResourceGroup->status = $status;
                    $newResourceGroup->is_auto_created = $is_auto_created_resourceGroup;
                    $newResourceGroup->is_accessible = $singleResourceGroup->is_accessable;
                    $newResourceGroup->save();

                    //for mode and type
                    $getMode = clone $getTagGroups;
                    $mode = $getMode->where('group_type', 'mode')->pluck('group_tag_id')->first();
                    if ($mode) {
                        $modes = json_decode($mode, true);
                        if (!empty($modes)) {
                            ResourceGroupTypeModes::where(['resource_group_id' => $singleResourceGroup->id, 'type_mode' => '1'])->delete();
                            foreach ($modes as $single_mode) {
                                if ($single_mode == '196') {
                                    $mode_id = '4';
                                } elseif ($single_mode == '197') {
                                    $mode_id = '5';
                                }
                                $resourceGroupMode = new ResourceGroupTypeModes();
                                $resourceGroupMode->resource_group_id = $singleResourceGroup->id;
                                $resourceGroupMode->type_mode = '1';
                                $resourceGroupMode->value = $mode_id;
                                $resourceGroupMode->save();
                            }
                        }
                    }

                    $getType = clone $getTagGroups;
                    $type = $getType->where('group_type', 'type')->pluck('group_tag_id')->first();
                    if ($type) {
                        $types = json_decode($type, true);
                        if (!empty($types)) {
                            ResourceGroupTypeModes::where(['resource_group_id' => $singleResourceGroup->id, 'type_mode' => '0'])->delete();
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
                                $resourceGroupType = new ResourceGroupTypeModes();
                                $resourceGroupType->resource_group_id = $singleResourceGroup->id;
                                $resourceGroupType->type_mode = '0';
                                $resourceGroupType->value = $type_id;
                                $resourceGroupType->save();
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
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e);

            return;
        }
    }
}
