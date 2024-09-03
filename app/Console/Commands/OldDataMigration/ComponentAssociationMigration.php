<?php

namespace App\Console\Commands\OldDataMigration;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\ComponentAssociation;
use App\Models\Lab;
use App\Models\LabProgram;
use App\Models\ResourceCollection;
use App\Models\ResourceGroup;
use App\Models\ResourceModule;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ComponentAssociationMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-old-data:component-association-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command is used for component's association with other component's, such as labs, programs, challenges, paths, modules, collections and groups";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            // For labs which includes challenges, paths, modules, collections and groups
            $getAllLabs = Lab::get();
            if ($getAllLabs->isNotEmpty()) {
                $this->info('Migrating for lab component association started.');
                foreach ($getAllLabs as $fetchedLab) {
                    // For Lab Challenge Component Associations
                    $labChallengeAssociationDatas = DB::connection('mysql2')->table('lab_challenges')->where('lab_id', $fetchedLab->id)->whereNotNull('challenge_id')->whereNull('deleted_at')->get();
                    if ($labChallengeAssociationDatas->isNotEmpty()) {
                        foreach ($labChallengeAssociationDatas as $labChallengeAssociation) {
                            $checkChallengeExist = Challenge::where('id', $labChallengeAssociation->challenge_id)->first();
                            if ($checkChallengeExist) {
                                if (!ComponentAssociation::where(['lab_id' => $fetchedLab->id, 'challenge_id' => $labChallengeAssociation->challenge_id])->exists()) {
                                    $newLabChallengeAssociation = new ComponentAssociation();
                                    $newLabChallengeAssociation->lab_id = $fetchedLab->id;
                                    $newLabChallengeAssociation->challenge_id = $labChallengeAssociation->challenge_id;
                                    $newLabChallengeAssociation->sequence = $labChallengeAssociation->sequence_no;
                                    $newLabChallengeAssociation->save();
                                }
                            }
                        }
                    }

                    // For Lab Challenge Path Component Associations
                    $labChallengePathAssociationDatas = DB::connection('mysql2')->table('lab_challenges')->where('lab_id', $fetchedLab->id)->whereNotNull('challenge_path_id')->whereNull('deleted_at')->get();
                    if ($labChallengePathAssociationDatas->isNotEmpty()) {
                        foreach ($labChallengePathAssociationDatas as $labChallengePathAssociation) {
                            $checkChallengePathExist = ChallengePath::where('id', $labChallengePathAssociation->challenge_path_id)->first();
                            if ($checkChallengePathExist) {
                                if (!ComponentAssociation::where(['lab_id' => $fetchedLab->id, 'challenge_path_id' => $labChallengePathAssociation->challenge_path_id])->exists()) {
                                    $newLabChallengePathAssociation = new ComponentAssociation();
                                    $newLabChallengePathAssociation->lab_id = $fetchedLab->id;
                                    $newLabChallengePathAssociation->challenge_path_id = $labChallengePathAssociation->challenge_path_id;
                                    $newLabChallengePathAssociation->sequence = $labChallengePathAssociation->sequence_no;
                                    $newLabChallengePathAssociation->save();
                                }
                            }
                        }
                    }

                    // For Lab Resource Module Component Associations
                    $labResourceModuleAssociationDatas = DB::connection('mysql2')->table('lab_resources')->where('lab_id', $fetchedLab->id)->whereNotNull('resources_id')->get();
                    if ($labResourceModuleAssociationDatas->isNotEmpty()) {
                        foreach ($labResourceModuleAssociationDatas as $labResourceModuleAssociation) {
                            $checkResourceModuleExist = ResourceModule::where('id', $labResourceModuleAssociation->resources_id)->first();
                            if ($checkResourceModuleExist) {
                                if (!ComponentAssociation::where(['lab_id' => $fetchedLab->id, 'resource_module_id' => $labResourceModuleAssociation->resources_id])->exists()) {
                                    $newLabResourceModuleAssociation = new ComponentAssociation();
                                    $newLabResourceModuleAssociation->lab_id = $fetchedLab->id;
                                    $newLabResourceModuleAssociation->resource_module_id = $labResourceModuleAssociation->resources_id;
                                    $newLabResourceModuleAssociation->sequence = $labResourceModuleAssociation->sequence_no;
                                    $newLabResourceModuleAssociation->save();
                                }
                            }
                        }
                    }

                    // For Lab Resource Collection Component Associations
                    $labResourceCollectionAssociationDatas = DB::connection('mysql2')->table('lab_resources')->where('lab_id', $fetchedLab->id)->whereNotNull('collection_id')->get();
                    if ($labResourceCollectionAssociationDatas->isNotEmpty()) {
                        foreach ($labResourceCollectionAssociationDatas as $labResourceCollectionAssociation) {
                            $checkResourceCollectionExist = ResourceCollection::where('id', $labResourceCollectionAssociation->collection_id)->first();
                            if ($checkResourceCollectionExist) {
                                if (!ComponentAssociation::where(['lab_id' => $fetchedLab->id, 'resource_Collection_id' => $labResourceCollectionAssociation->collection_id])->exists()) {
                                    $newLabResourceCollectionAssociation = new ComponentAssociation();
                                    $newLabResourceCollectionAssociation->lab_id = $fetchedLab->id;
                                    $newLabResourceCollectionAssociation->resource_Collection_id = $labResourceCollectionAssociation->collection_id;
                                    $newLabResourceCollectionAssociation->sequence = $labResourceCollectionAssociation->sequence_no;
                                    $newLabResourceCollectionAssociation->save();
                                }
                            }
                        }
                    }

                    // For Lab Resource Group Component Associations
                    $labResourceGroupAssociationDatas = DB::connection('mysql2')->table('lab_resources')->where('lab_id', $fetchedLab->id)->whereNotNull('group_id')->get();
                    if ($labResourceGroupAssociationDatas->isNotEmpty()) {
                        foreach ($labResourceGroupAssociationDatas as $labResourceGroupAssociation) {
                            $checkResourceGroupExist = ResourceGroup::where('id', $labResourceGroupAssociation->group_id)->first();
                            if ($checkResourceGroupExist) {
                                if (!ComponentAssociation::where(['lab_id' => $fetchedLab->id, 'resource_group_id' => $labResourceGroupAssociation->group_id])->exists()) {
                                    $newLabResourceGroupAssociation = new ComponentAssociation();
                                    $newLabResourceGroupAssociation->lab_id = $fetchedLab->id;
                                    $newLabResourceGroupAssociation->resource_group_id = $labResourceGroupAssociation->group_id;
                                    $newLabResourceGroupAssociation->sequence = $labResourceGroupAssociation->sequence_no;
                                    $newLabResourceGroupAssociation->save();
                                }
                            }
                        }
                    }
                }
                $this->info('Migrating for lab component association ended.');
            }

            // For challenges which includes programs, modules, collections and groups
            $getAllChallenges = Challenge::get();
            if ($getAllChallenges->isNotEmpty()) {
                $this->info('Migrating for challenge component association started.');
                $getAllChallenges->each(function ($fetchedChallenge) {
                    $sequence = 1;
                    $challengeLabProgramAssociationDatas = DB::connection('mysql2')->table('challenge_groups')->where('challenge_id', $fetchedChallenge->id)->whereNull('deleted_at')->get();

                    $challengeLabProgramAssociationDatas->each(function ($challengeLabProgramAssociation) use ($fetchedChallenge, &$sequence) {
                        $labProgramArray = array_filter(array_map('trim', explode(',', $challengeLabProgramAssociation->lab_group)));

                        if (!empty($labProgramArray)) {
                            foreach ($labProgramArray as $labProgramId) {
                                $labProgram = LabProgram::where('id', $labProgramId)->first();
                                if ($labProgram) {
                                    if (!ComponentAssociation::where(['challenge_id' => $fetchedChallenge->id, 'lab_program_id' => $labProgramId])->exists()) {
                                        ComponentAssociation::create([
                                            'challenge_id'   => $fetchedChallenge->id,
                                            'lab_program_id' => $labProgramId,
                                            'sequence'       => $sequence++,
                                        ]);
                                    }
                                }
                            }
                        }
                    });

                    $challengeResourceModuleAssociationDatas = DB::connection('mysql2')->table('admin_challenge_resource_datas')->where('admin_challenge_id', $fetchedChallenge->id)->get();
                    if ($challengeResourceModuleAssociationDatas->isNotEmpty()) {
                        foreach ($challengeResourceModuleAssociationDatas as $challengeResourceModuleAssociation) {
                            $resourceModule = ResourceModule::where('id', $challengeResourceModuleAssociation->resource_datas_id)->first();
                            if ($resourceModule) {
                                if (!ComponentAssociation::where(['challenge_id' => $fetchedChallenge->id, 'resource_module_id' => $challengeResourceModuleAssociation->resource_datas_id])->exists()) {
                                    ComponentAssociation::create([
                                        'challenge_id'       => $fetchedChallenge->id,
                                        'resource_module_id' => $challengeResourceModuleAssociation->resource_datas_id,
                                        'sequence'           => $sequence++,
                                    ]);
                                }
                            }
                        }
                    }

                    $challengeResourceCollectionAssociationDatas = DB::connection('mysql2')->table('resource_collection_for_lab_and_challenges')->where('challenge_id', $fetchedChallenge->id)->get();
                    if ($challengeResourceCollectionAssociationDatas->isNotEmpty()) {
                        foreach ($challengeResourceCollectionAssociationDatas as $challengeResourceCollectionAssociation) {
                            $resourceCollection = ResourceCollection::where('id', $challengeResourceCollectionAssociation->resource_collection_id)->first();
                            if ($resourceCollection) {
                                if (!ComponentAssociation::where(['challenge_id' => $fetchedChallenge->id, 'resource_collection_id' => $challengeResourceCollectionAssociation->resource_collection_id])->exists()) {
                                    ComponentAssociation::create([
                                        'challenge_id'           => $fetchedChallenge->id,
                                        'resource_collection_id' => $challengeResourceCollectionAssociation->resource_collection_id,
                                        'sequence'               => $sequence++,
                                    ]);
                                }
                            }
                        }
                    }

                    $challengeLabProgramAssociationDatas->each(function ($challengeResourceGroupAssociation) use ($fetchedChallenge, &$sequence) {
                        $resourceGroupArray = array_filter(array_map('trim', explode(',', $challengeResourceGroupAssociation->resource_group)));

                        if (!empty($resourceGroupArray)) {
                            foreach ($resourceGroupArray as $resourceGroupId) {
                                $resourceGroup = ResourceGroup::where('id', $resourceGroupId)->first();
                                if ($resourceGroup) {
                                    if (!ComponentAssociation::where(['challenge_id' => $fetchedChallenge->id, 'resource_group_id' => $resourceGroupId])->exists()) {
                                        ComponentAssociation::create([
                                            'challenge_id'      => $fetchedChallenge->id,
                                            'resource_group_id' => $resourceGroupId,
                                            'sequence'          => $sequence++,
                                        ]);
                                    }
                                }
                            }
                        }
                    });
                });
                $this->info('Migrating for challenge component association ended.');
            }

            // For resource collections which includes programs, modules, collections and groups
            $getAllResourceCollections = ResourceCollection::get();
            if ($getAllResourceCollections->isNotEmpty()) {
                $this->info('Migrating for resource collection component association started.');
                $getAllResourceCollections->each(function ($fetchedResourceCollection) {
                    $sequence = 1;
                    $resourceCollectionAssociationDatas = DB::connection('mysql2')->table('resourcegroup')->where('id', $fetchedResourceCollection->id)->first();
                    if ($resourceCollectionAssociationDatas) {
                        if (!empty($resourceCollectionAssociationDatas->assoicated_lab)) {
                            $collectionLabIds = json_decode($resourceCollectionAssociationDatas->assoicated_lab);
                            if (!empty($collectionLabIds)) {
                                foreach ($collectionLabIds as $labId) {
                                    $checkLabExist = Lab::where('id', $labId)->first();
                                    if ($checkLabExist) {
                                        if (!ComponentAssociation::where(['lab_id' => $labId, 'resource_collection_id' => $fetchedResourceCollection->id])->exists()) {
                                            ComponentAssociation::create([
                                                'resource_collection_id'    => $fetchedResourceCollection->id,
                                                'lab_id'                    => $labId,
                                                'sequence'                  => $sequence++,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($resourceCollectionAssociationDatas->assoicated_challange)) {
                            $collectionChallengeIds = json_decode($resourceCollectionAssociationDatas->assoicated_challange);
                            if (!empty($collectionChallengeIds)) {
                                foreach ($collectionChallengeIds as $challengeId) {
                                    $checkChallengeExist = Challenge::where('id', $challengeId)->first();
                                    if ($checkChallengeExist) {
                                        if (!ComponentAssociation::where(['challenge_id' => $challengeId, 'resource_collection_id' => $fetchedResourceCollection->id])->exists()) {
                                            ComponentAssociation::create([
                                                'resource_collection_id'    => $fetchedResourceCollection->id,
                                                'challenge_id'              => $challengeId,
                                                'sequence'                  => $sequence++,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($resourceCollectionAssociationDatas->resource_id)) {
                            $collectionResourceIds = json_decode($resourceCollectionAssociationDatas->resource_id);
                            if (!empty($collectionResourceIds)) {
                                foreach ($collectionResourceIds as $resourceId) {
                                    $checkResourceModuleExist = ResourceModule::where('id', $resourceId)->first();
                                    if ($checkResourceModuleExist) {
                                        if (!ComponentAssociation::where(['resource_module_id' => $resourceId, 'resource_collection_id' => $fetchedResourceCollection->id])->exists()) {
                                            ComponentAssociation::create([
                                                'resource_collection_id'    => $fetchedResourceCollection->id,
                                                'resource_module_id'        => $resourceId,
                                                'sequence'                  => $sequence++,
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                $this->info('Migrating for resource collection component association ended.');
            }

            // For resource groups which includes modules, collections
            $getAllResourceGroups = ResourceGroup::get();
            if ($getAllResourceGroups->isNotEmpty()) {
                $this->info('Migrating for resource group component association started.');
                $getAllResourceGroups->each(function ($fetchedResourceGroup) {
                    $sequence = 1;
                    $resourceModuleResourceGroupAssociationDatas = DB::connection('mysql2')->table('groups')->where(['id' => $fetchedResourceGroup->id, 'type' => 'resource'])->whereNull('deleted_at')->first();
                    if ($resourceModuleResourceGroupAssociationDatas) {
                        if ($resourceModuleResourceGroupAssociationDatas->resource_id != null) {
                            $resourceModuleArray = explode(',', $resourceModuleResourceGroupAssociationDatas->resource_id);
                            foreach ($resourceModuleArray as $resourceId) {
                                $checkResourceModuleExist = ResourceModule::where('id', $resourceId)->first();
                                if ($checkResourceModuleExist) {
                                    if (!ComponentAssociation::where(['resource_module_id' => $resourceId, 'resource_group_id' => $fetchedResourceGroup->id])->exists()) {
                                        ComponentAssociation::create([
                                            'resource_group_id'         => $fetchedResourceGroup->id,
                                            'resource_module_id'        => $resourceId,
                                            'sequence'                  => $sequence++,
                                        ]);
                                    }
                                }
                            }
                        }

                        if ($resourceModuleResourceGroupAssociationDatas->collection_id != null) {
                            $resourceCollectionArray = explode(',', $resourceModuleResourceGroupAssociationDatas->collection_id);
                            foreach ($resourceCollectionArray as $collectionId) {
                                $checkResourceModuleExist = ResourceCollection::where('id', $collectionId)->first();
                                if ($checkResourceModuleExist) {
                                    if (!ComponentAssociation::where(['resource_collection_id' => $collectionId, 'resource_group_id' => $fetchedResourceGroup->id])->exists()) {
                                        ComponentAssociation::create([
                                            'resource_group_id'         => $fetchedResourceGroup->id,
                                            'resource_collection_id'    => $collectionId,
                                            'sequence'                  => $sequence++,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                });
                $this->info('Migrating for resource group component association ended.');
            }
            DB::commit();

            return;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
            $this->error($e->getMessage());

            return;
        }
    }
}
