<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\ComponentAssociation;
use App\Services\Manage\ResourceModuleService;
use Exception;

class ComponentAssociationService
{
    public static function labAssociation($request, $lab)
    {
        try {
            if ($request->has('lab_programs')) {
                $sequence = 1;
                if (count($request->lab_programs) > 0) {
                    foreach ($request->lab_programs as $lab_program) {
                        $getLabProgramId = LabProgramService::getLabProgramBasedOnUUID($lab_program);
                        $checkLabProgramAssociation = ComponentAssociation::where('lab_program_id', $getLabProgramId->id)->whereNotNull('lab_id')->orderBy('created_at', 'desc')->first();
                        if ($checkLabProgramAssociation) {
                            $sequence = $checkLabProgramAssociation->sequence + 1;
                        }

                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->lab_program_id = $getLabProgramId->id;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('challenges')) {
                $sequence = 1;
                if (count($request->challenges) > 0) {
                    foreach ($request->challenges as $challenge) {
                        $getChallengeId = ChallengeService::getChallengeBasedOnUUID($challenge);
                        $checkChallengeAssociation = ComponentAssociation::where('challenge_id', $getChallengeId->id)->whereNotNull('lab_id')->orderBy('created_at', 'desc')->first();
                        if ($checkChallengeAssociation) {
                            $sequence = $checkChallengeAssociation->sequence + 1;
                        }
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->challenge_id = $getChallengeId->id;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('challenge_paths')) {
                $sequence = 1;
                if (count($request->challenge_paths) > 0) {
                    foreach ($request->challenge_paths as $challenge_path) {
                        $getChallengePathId = ChallengePathService::getChallengePathBasedOnUUID($challenge_path);
                        $checkChallengePathAssociation = ComponentAssociation::where('challenge_path_id', $getChallengePathId->id)->whereNotNull('lab_id')->orderBy('created_at', 'desc')->first();
                        if ($checkChallengePathAssociation) {
                            $sequence = $checkChallengePathAssociation->sequence + 1;
                        }

                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->challenge_path_id = $getChallengePathId->id;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('resource_modules') && $request->resource_modules != false) {
                $sequence = 1;
                if (count($request->resource_modules) > 0) {
                    foreach ($request->resource_modules as $resource_module) {
                        $getResourceModuleId = ResourceModuleService::getResourceModuleBasedOnUUID($resource_module);
                        $checkResourceModuleAssociation = ComponentAssociation::where('resource_module_id', $getResourceModuleId->id)->whereNotNull('lab_id')->orderBy('created_at', 'desc')->first();
                        if ($checkResourceModuleAssociation) {
                            $sequence = $checkResourceModuleAssociation->sequence + 1;
                        }
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->resource_module_id = $getResourceModuleId->id;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('resource_groups')) {
                $sequence = 1;
                if (count($request->resource_groups) > 0) {
                    foreach ($request->resource_groups as $resource_group) {
                        $getResourceGroupId = ResourceGroupService::getResourceGroupBasedOnUUID($resource_group);
                        $checkResourceGroupAssociation = ComponentAssociation::where('resource_group_id', $getResourceGroupId->id)->whereNotNull('lab_id')->orderBy('created_at', 'desc')->first();
                        if ($checkResourceGroupAssociation) {
                            $sequence = $checkResourceGroupAssociation->sequence + 1;
                        }
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->resource_group_id = $getResourceGroupId->id;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('resource_collections')) {
                $sequence = 1;
                if (count($request->resource_collections) > 0) {
                    foreach ($request->resource_collections as $resource_collection) {
                        $getResourceCollectionId = ResourceCollectionService::getResourceCollectionBasedOnUUID($resource_collection);
                        $checkResourceCollectionAssociation = ComponentAssociation::where('resource_collection_id', $getResourceCollectionId->id)->whereNotNull('lab_id')->orderBy('created_at', 'desc')->first();
                        if ($checkResourceCollectionAssociation) {
                            $sequence = $checkResourceCollectionAssociation->sequence + 1;
                        }
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->resource_collection_id = $getResourceCollectionId->id;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                        $sequence++;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateLabAssociation($request, $lab)
    {
        try {
            if ($request->has('lab_programs')) {
                $sequence = 1;
                if (count($request->lab_programs) > 0) {
                    $getLabProgramIds = LabProgramService::getLabProgramIdBasedOnUUIDArray($request->lab_programs);
                    $existComponentAssociation = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['lab_program_id', '!=', null],
                    ])->pluck('lab_program_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $getLabProgramIds);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('lab_id', $lab)->whereIn('lab_program_id', $nonExistingIds)->delete();
                    $newComponentAssociations = array_diff($getLabProgramIds, $existComponentAssociation);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['lab_program_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociations as $lab_program) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->lab_program_id = $lab_program;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('challenges')) {
                $sequence = 1;
                if (count($request->challenges) > 0) {
                    $getChallengeIds = ChallengeService::getChallengeBasedOnUUIDArray($request->challenges);
                    $existComponentAssociationchallenge = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['challenge_id', '!=', null],
                    ])->pluck('challenge_id')->all();
                    $nonExistingIdsChallengeId = array_diff($existComponentAssociationchallenge, $getChallengeIds);
                    $deleteNonExistingComponentAssociationChallenges = ComponentAssociation::where('lab_id', $lab)->whereIn('challenge_id', $nonExistingIdsChallengeId)->delete();
                    $newComponentAssociationChallenge = array_diff($getChallengeIds, $existComponentAssociationchallenge);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['challenge_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociationChallenge as $challenge) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->challenge_id = $challenge;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                    }
                }
            }

            if ($request->has('challenge_paths')) {
                $sequence = 1;
                if (count($request->challenge_paths) > 0) {
                    $getChallengePathIds = ChallengePathService::getChallengePathBasedOnUUIDArray($request->challenge_paths);
                    $existComponentAssociationChallengePathId = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['challenge_path_id', '!=', null],
                    ])->pluck('challenge_path_id')->all();
                    $nonExistingIdsChallengePathId = array_diff($existComponentAssociationChallengePathId, $getChallengePathIds);
                    $deleteNonExistingComponentAssociationChallengesPath = ComponentAssociation::where('lab_id', $lab)->whereIn('challenge_path_id', $nonExistingIdsChallengePathId)->delete();
                    $newComponentAssociationChallengePathId = array_diff($getChallengePathIds, $existComponentAssociationChallengePathId);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['challenge_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociationChallengePathId as $challenge_path) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->challenge_path_id = $challenge_path;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                    }
                }
            }

            if ($request->has('resource_modules')) {
                $sequence = 1;
                if (count($request->resource_modules) > 0) {
                    $getResourceGroupIds = ResourceModuleService::getResourceModuleBasedOnUUIDArray($request->resource_modules);
                    $existComponentAssociationResourceModuleId = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['resource_module_id', '!=', null],
                    ])->pluck('resource_module_id')->all();
                    $nonExistingIdsResourceModuleId = array_diff($existComponentAssociationResourceModuleId, $getResourceGroupIds);
                    $deleteNonExistingResourceModuleId = ComponentAssociation::where('lab_id', $lab)->whereIn('resource_module_id', $nonExistingIdsResourceModuleId)->delete();

                    $newComponentAssociationResourceModuleId = array_diff($getResourceGroupIds, $existComponentAssociationResourceModuleId);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['resource_module_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociationResourceModuleId as $resource_module) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->resource_module_id = $resource_module;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                    }
                }
            }

            if ($request->has('resource_collections')) {
                $sequence = 1;
                if (count($request->resource_collections) > 0) {
                    $getResourceCollectionIds = ResourceCollectionService::getResourceCollectionBasedOnUUIDArray($request->resource_collections);
                    $existComponentAssociationResourceCollectionId = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['resource_collection_id', '!=', null],
                    ])->pluck('resource_collection_id')->all();
                    $nonExistingIdsResourceCollectionId = array_diff($existComponentAssociationResourceCollectionId, $getResourceCollectionIds);
                    $deleteNonExistingResourceGroupId = ComponentAssociation::where('lab_id', $lab)->whereIn('resource_collection_id', $nonExistingIdsResourceCollectionId)->delete();
                    $newComponentAssociationResourceGroupId = array_diff($getResourceCollectionIds, $existComponentAssociationResourceCollectionId);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['resource_collection_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociationResourceGroupId as $resource_collection) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->resource_collection_id = $resource_collection;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                    }
                }
            }

            if ($request->has('resource_groups')) {
                $sequence = 1;
                if (count($request->resource_groups) > 0) {
                    $getResourceGroupIds = ResourceGroupService::getResourceGroupBasedOnUUIDArray($request->resource_groups);
                    $existComponentAssociationResourceGroupId = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['resource_group_id', '!=', null],
                    ])->pluck('resource_group_id')->all();
                    $nonExistingIdsResourceGroupId = array_diff($existComponentAssociationResourceGroupId, $getResourceGroupIds);
                    $deleteNonExistingResourceGroupId = ComponentAssociation::where('lab_id', $lab)->whereIn('resource_group_id', $nonExistingIdsResourceGroupId)->delete();
                    $newComponentAssociationResourceGroupId = array_diff($getResourceGroupIds, $existComponentAssociationResourceGroupId);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab],
                        ['resource_group_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociationResourceGroupId as $resource_group) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_id = $lab;
                        $labSkillsGroupsStack->resource_group_id = $resource_group;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deletelabAssociation($lab)
    {
        try {
            $getComponentAssociation = ComponentAssociation::select('id')->where('lab_id', $lab)->get()->all();
            if ($getComponentAssociation) {
                $deleteComponentAssociation = ComponentAssociation::whereIn('id', $getComponentAssociation)->delete();
                if (!$deleteComponentAssociation) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addAssociatedLabWithChallenge($request, $challenge)
    {
        try {
            if (!empty($request->associativeLab)) {
                if (ComponentAssociation::where('challenge_id', $challenge->id)->whereNotNull('lab_id')->exists()) {
                    ComponentAssociation::where('challenge_id', $challenge->id)->whereNotNull('lab_id')->delete();
                }
                $labNewArray = [];
                foreach ($request->associativeLab as $key => $lab) {
                    $labData['challenge_id'] = $challenge->id;
                    $labData['lab_id'] = $lab;
                    $labData['sequence'] = $key + 1;
                    $labNewArray[] = $labData;
                }
                ComponentAssociation::insert($labNewArray);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function addAssociatedResourceModuleWithChallenge($request, $challenge)
    {
        try {
            if (!empty($request->associativeResourceModule)) {
                if (ComponentAssociation::where('challenge_id', $challenge->id)->whereNotNull('resource_module_id')->exists()) {
                    ComponentAssociation::where('challenge_id', $challenge->id)->whereNotNull('resource_module_id')->delete();
                }
                $resourceModuleNewArray = [];
                foreach ($request->associativeResourceModule as $key => $resourceModule) {
                    $resourceModuleData['challenge_id'] = $challenge->id;
                    $resourceModuleData['resource_module_id'] = (int) $resourceModule;
                    $resourceModuleData['sequence'] = $key + 1;
                    $resourceModuleNewArray[] = $resourceModuleData;
                }
                ComponentAssociation::insert($resourceModuleNewArray);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeAssociatedLab($challenge)
    {
        try {
            return ComponentAssociation::where(['challenge_id' => $challenge->id])->pluck('lab_id');
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeAssociatedResourceModule($challenge)
    {
        try {
            return ComponentAssociation::where(['challenge_id' => (int) $challenge->id])->pluck('resource_module_id');
        } catch (Exception $e) {
            return false;
        }
    }
}
