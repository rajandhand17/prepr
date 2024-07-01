<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ComponentAssociation;
use App\Models\Lab;
use Exception;

class ComponentAssociationService
{
    public function labAssociation($request, $lab)
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
                        $labSkillsGroupsStack->lab_id = $lab->id;
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
                        $labSkillsGroupsStack->lab_id = $lab->id;
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
                        $labSkillsGroupsStack->lab_id = $lab->id;
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
                        $labSkillsGroupsStack->lab_id = $lab->id;
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
                        $labSkillsGroupsStack->lab_id = $lab->id;
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
                        $labSkillsGroupsStack->lab_id = $lab->id;
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

    public function updateLabAssociation($request, $lab_id)
    {
        try {
            if ($request->has('lab_programs')) {
                $sequence = 1;
                if (count($request->lab_programs) > 0) {
                    $getLabProgramIds = LabProgramService::getLabProgramIdBasedOnUUIDArray($request->lab_programs);
                    $existComponentAssociation = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['lab_program_id', '!=', null],
                    ])->pluck('lab_program_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $getLabProgramIds);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('lab_id', $lab_id)->whereIn('lab_program_id', $nonExistingIds)->delete();
                    $newComponentAssociations = array_diff($getLabProgramIds, $existComponentAssociation);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
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
                        $labSkillsGroupsStack->lab_id = $lab_id;
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
                        ['lab_id', '=', $lab_id],
                        ['challenge_id', '!=', null],
                    ])->pluck('challenge_id')->all();
                    $nonExistingIdsChallengeId = array_diff($existComponentAssociationchallenge, $getChallengeIds);
                    $deleteNonExistingComponentAssociationChallenges = ComponentAssociation::where('lab_id', $lab_id)->whereIn('challenge_id', $nonExistingIdsChallengeId)->delete();
                    $newComponentAssociationChallenge = array_diff($getChallengeIds, $existComponentAssociationchallenge);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
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
                        $labSkillsGroupsStack->lab_id = $lab_id;
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
                        ['lab_id', '=', $lab_id],
                        ['challenge_path_id', '!=', null],
                    ])->pluck('challenge_path_id')->all();
                    $nonExistingIdsChallengePathId = array_diff($existComponentAssociationChallengePathId, $getChallengePathIds);
                    $deleteNonExistingComponentAssociationChallengesPath = ComponentAssociation::where('lab_id', $lab_id)->whereIn('challenge_path_id', $nonExistingIdsChallengePathId)->delete();
                    $newComponentAssociationChallengePathId = array_diff($getChallengePathIds, $existComponentAssociationChallengePathId);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
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
                        $labSkillsGroupsStack->lab_id = $lab_id;
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
                        ['lab_id', '=', $lab_id],
                        ['resource_module_id', '!=', null],
                    ])->pluck('resource_module_id')->all();
                    $nonExistingIdsResourceModuleId = array_diff($existComponentAssociationResourceModuleId, $getResourceGroupIds);
                    $deleteNonExistingResourceModuleId = ComponentAssociation::where('lab_id', $lab_id)->whereIn('resource_module_id', $nonExistingIdsResourceModuleId)->delete();
                    $newComponentAssociationResourceModuleId = array_diff($getResourceGroupIds, $existComponentAssociationResourceModuleId);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
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
                        $labSkillsGroupsStack->lab_id = $lab_id;
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
                        ['lab_id', '=', $lab_id],
                        ['resource_collection_id', '!=', null],
                    ])->pluck('resource_collection_id')->all();
                    $nonExistingIdsResourceCollectionId = array_diff($existComponentAssociationResourceCollectionId, $getResourceCollectionIds);
                    $deleteNonExistingResourceGroupId = ComponentAssociation::where('lab_id', $lab_id)->whereIn('resource_collection_id', $nonExistingIdsResourceCollectionId)->delete();
                    $newComponentAssociationResourceGroupId = array_diff($getResourceCollectionIds, $existComponentAssociationResourceCollectionId);
                    $sequences = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
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
                        $labSkillsGroupsStack->lab_id = $lab_id;
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
                        ['lab_id', '=', $lab_id],
                        ['resource_group_id', '!=', null],
                    ])->pluck('resource_group_id')->all();
                    $nonExistingIdsResourceGroupId = array_diff($existComponentAssociationResourceGroupId, $getResourceGroupIds);
                    $deleteNonExistingResourceGroupId = ComponentAssociation::where('lab_id', $lab_id)->whereIn('resource_group_id', $nonExistingIdsResourceGroupId)->delete();
                    $newComponentAssociationResourceGroupId = array_diff($getResourceGroupIds, $existComponentAssociationResourceGroupId);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
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
                        $labSkillsGroupsStack->lab_id = $lab_id;
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

    public static function deletelabAssociation($lab_id)
    {
        try {
            $getComponentAssociation = ComponentAssociation::select('id')->where('lab_id', $lab_id)->get()->all();
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

    public function labProgramAssociation($request, $labProgram)
    {
        if ($request->has('lab_ids')) {
            $sequence = 1;
            if (count($request->lab_ids) > 0) {
                foreach ($request->lab_ids as $lab) {
                    $lab_id = Lab::where('uuid', $lab)->select('id')->first()->id;
                    $labSkillsGroupsStack = new ComponentAssociation();
                    $labSkillsGroupsStack->lab_id = $lab_id;
                    $labSkillsGroupsStack->lab_program_id = $labProgram->id;
                    $labSkillsGroupsStack->sequence = $sequence;
                    $labSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        return true;
    }

    public function updateLabProgramAssociation($request, $lab_programs)
    {
        try {
            if ($request->has('lab_ids')) {
                $sequence = 1;
                $getLabId = LabService::getLabIdBasedOnUUIDArray($request->lab_ids);
                $request->merge(['lab_ids' => $getLabId]);
                if (count($request->lab_ids) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['lab_program_id', '=', $lab_programs],
                        ['lab_id', '!=', null],
                    ])->pluck('lab_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->lab_ids);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('lab_program_id', $lab_programs)->whereIn('lab_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->lab_ids, $existComponentAssociation);

                    $sequences = ComponentAssociation::where([
                        ['lab_program_id', '=', $lab_programs],
                        ['lab_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    }
                    foreach ($newComponentAssociation as $lab_id) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->lab_program_id = $lab_programs;
                        $labSkillsGroupsStack->lab_id = $lab_id;
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

    public function createChallengePathAssociation($request, $challengePathId)
    {
        try {
            if ($request->has('challenge_ids')) {
                $getChallengeIds = ChallengeService::getChallengeBasedOnUUIDArray($request->challenge_ids);
                $request->merge(['challenge_ids' => $getChallengeIds]);
                if (count($getChallengeIds) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['challenge_id', '!=', null],
                    ])->pluck('challenge_id')->all();
                    $nonExistingIds = array_diff($getChallengeIds, $existComponentAssociation);

                    ComponentAssociation::where('challenge_path_id', $challengePathId)->whereIn('challenge_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->challenge_ids, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['challenge_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    $sequence = 1;
                    foreach ($newComponentAssociation as $challenge_id) {
                        $sequence++;
                        $challengeAssociation = new ComponentAssociation();
                        $challengeAssociation->challenge_path_id = $challengePathId;
                        $challengeAssociation->challenge_id = $challenge_id;
                        $challengeAssociation->sequence = $sequence;
                        $challengeAssociation->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateChallengePathAssociation($request, $challengePathId)
    {
        try {
            if ($request->has('challenge_ids')) {
                $sequence = 1;
                $getChallengeIds = ChallengeService::getChallengeBasedOnUUIDArray($request->challenge_ids);
                $request->merge(['challenge_ids' => $getChallengeIds]);
                if (count($request->challenge_ids) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['challenge_id', '!=', null],
                    ])->pluck('challenge_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->challenge_ids);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('challenge_path_id', $challengePathId)->whereIn('challenge_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->challenge_ids, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['challenge_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    foreach ($newComponentAssociation as $challenge_id) {
                        $sequence++;
                        $labSkillsGroupsStack = new ComponentAssociation();
                        $labSkillsGroupsStack->challenge_path_id = $challengePathId;
                        $labSkillsGroupsStack->challenge_id = $challenge_id;
                        $labSkillsGroupsStack->sequence = $sequence;
                        $labSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteChallengePathAssociation($challenge_path_id)
    {
        try {
            $getComponentAssociation = ComponentAssociation::where('challenge_path_id', $challenge_path_id)->pluck('id');
            if ($getComponentAssociation->isNotEmpty()) {
                $deleteComponentAssociation = ComponentAssociation::whereIn('id', $getComponentAssociation)->delete();
                if (!$deleteComponentAssociation) {
                    return false;
                }

                return true;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createResourceCollectionAssociation($request, $resourceCollectionId)
    {
        try {
            $sequence = 1;
            if ($request->has('lab_ids') && count($request->lab_ids) > 0) {
                $getLabId = LabService::getLabIdBasedOnUUIDArray($request->lab_ids);
                $sequence = ComponentAssociation::where([
                    ['resource_collection_id', '=', $resourceCollectionId],
                    ['lab_id', '!=', null],
                ])->select('sequence')->orderBy('id', 'desc')->first();
                if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                    $sequence = $sequence->sequence;
                }
                foreach ($getLabId as $labId) {
                    $sequence++;
                    $resourceCollectionLab = new ComponentAssociation();
                    $resourceCollectionLab->resource_collection_id = $resourceCollectionId;
                    $resourceCollectionLab->lab_id = $labId;
                    $resourceCollectionLab->sequence = $sequence;
                    $resourceCollectionLab->save();
                }
            }

            if ($request->has('challenge_ids') && count($request->challenge_ids) > 0) {
                $getChallengeId = ChallengeService::getChallengeBasedOnUUIDArray($request->challenge_ids);
                $sequence = ComponentAssociation::where([
                    ['resource_collection_id', '=', $resourceCollectionId],
                    ['challenge_id', '!=', null],
                ])->select('sequence')->orderBy('id', 'desc')->first();
                if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                    $sequence = $sequence->sequence;
                }
                foreach ($getChallengeId as $challengeId) {
                    $sequence++;
                    $resourceCollectionChallenge = new ComponentAssociation();
                    $resourceCollectionChallenge->resource_collection_id = $resourceCollectionId;
                    $resourceCollectionChallenge->challenge_id = $challengeId;
                    $resourceCollectionChallenge->sequence = $sequence;
                    $resourceCollectionChallenge->save();
                }
            }

            if ($request->has('resource_ids') && count($request->resource_ids) > 0) {
                $getResourceModuleIds = ResourceModuleService::getResourceModuleBasedOnUUIDArray($request->resource_ids);
                $sequence = ComponentAssociation::where([
                    ['resource_collection_id', '=', $resourceCollectionId],
                    ['resource_module_id', '!=', null],
                ])->select('sequence')->orderBy('id', 'desc')->first();
                if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                    $sequence = $sequence->sequence;
                }
                foreach ($getResourceModuleIds as $resourceModuleId) {
                    $sequence++;
                    $resourceCollectionResourceModule = new ComponentAssociation();
                    $resourceCollectionResourceModule->resource_collection_id = $resourceCollectionId;
                    $resourceCollectionResourceModule->resource_module_id = $resourceModuleId;
                    $resourceCollectionResourceModule->sequence = $sequence;
                    $resourceCollectionResourceModule->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateResourceCollectionAssociation($request, $resourceCollectionId)
    {
        try {
            $sequence = 1;
            if ($request->has('lab_ids') && count($request->lab_ids) > 0) {
                $getLabId = LabService::getLabIdBasedOnUUIDArray($request->lab_ids);
                $request->merge(['lab_ids' => $getLabId]);
                if (count($request->lab_ids) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['resource_collection_id', '=', $resourceCollectionId],
                        ['lab_id', '!=', null],
                    ])->pluck('lab_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->lab_ids);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('resource_collection_id', $resourceCollectionId)->whereIn('lab_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->lab_ids, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['resource_collection_id', '=', $resourceCollectionId],
                        ['lab_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                        $sequence = $sequence->sequence;
                    }
                    foreach ($newComponentAssociation as $labId) {
                        $sequence++;
                        $resourceCollectionLab = new ComponentAssociation();
                        $resourceCollectionLab->resource_collection_id = $resourceCollectionId;
                        $resourceCollectionLab->lab_id = $labId;
                        $resourceCollectionLab->sequence = $sequence;
                        $resourceCollectionLab->save();
                    }
                }
            }

            if ($request->has('challenge_ids') && count($request->challenge_ids) > 0) {
                $getChallengeIds = ChallengeService::getChallengeBasedOnUUIDArray($request->challenge_ids);
                $request->merge(['challenge_ids' => $getChallengeIds]);
                if (count($request->challenge_ids) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['resource_collection_id', '=', $resourceCollectionId],
                        ['challenge_id', '!=', null],
                    ])->pluck('challenge_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->challenge_ids);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('resource_collection_id', $resourceCollectionId)->whereIn('challenge_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->challenge_ids, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['resource_collection_id', '=', $resourceCollectionId],
                        ['challenge_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                        $sequence = $sequence->sequence;
                    }
                    foreach ($newComponentAssociation as $challengeId) {
                        $sequence++;
                        $resourceCollectionChallenge = new ComponentAssociation();
                        $resourceCollectionChallenge->resource_collection_id = $resourceCollectionId;
                        $resourceCollectionChallenge->challenge_id = $challengeId;
                        $resourceCollectionChallenge->sequence = $sequence;
                        $resourceCollectionChallenge->save();
                    }
                }
            }

            if ($request->has('resource_ids') && count($request->resource_ids) > 0) {
                $getResourceModuleIds = ResourceModuleService::getResourceModuleBasedOnUUIDArray($request->resource_ids);
                $request->merge(['resource_ids' => $getResourceModuleIds]);
                if (count($request->resource_ids) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['resource_collection_id', '=', $resourceCollectionId],
                        ['resource_module_id', '!=', null],
                    ])->pluck('resource_module_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->resource_ids);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('resource_collection_id', $resourceCollectionId)->whereIn('resource_module_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->resource_ids, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['resource_collection_id', '=', $resourceCollectionId],
                        ['resource_module_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                        $sequence = $sequence->sequence;
                    }
                    foreach ($newComponentAssociation as $resourceModuleId) {
                        $sequence++;
                        $resourceCollectionResourceModule = new ComponentAssociation();
                        $resourceCollectionResourceModule->resource_collection_id = $resourceCollectionId;
                        $resourceCollectionResourceModule->resource_module_id = $resourceModuleId;
                        $resourceCollectionResourceModule->sequence = $sequence;
                        $resourceCollectionResourceModule->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceCollectionAssociation($resource_collection_id)
    {
        try {
            $checkExistsComponentAssociation = ComponentAssociation::select('id')->where('resource_collection_id', $resource_collection_id)->pluck('id');
            if ($checkExistsComponentAssociation) {
                $deleteComponentAssociation = ComponentAssociation::whereIn('id', $checkExistsComponentAssociation)->delete();
                if (!$deleteComponentAssociation) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createResourceGroupComponentAssociation($request, $resourceGroupId)
    {
        try {
            if ($request->has('resource_ids') && count($request->resource_ids) > 0) {
                $getResourceModuleIds = ResourceModuleService::getResourceModuleBasedOnUUIDArray($request->resource_ids);
                $sequence = ComponentAssociation::where([
                    ['resource_group_id', '=', $resourceGroupId],
                    ['resource_module_id', '!=', null],
                ])->select('sequence')->orderBy('id', 'desc')->first();
                if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                    $sequence = $sequence->sequence;
                }
                foreach ($getResourceModuleIds as $resourceModuleId) {
                    $sequence++;
                    $resourceCollectionResourceModule = new ComponentAssociation();
                    $resourceCollectionResourceModule->resource_group_id = $resourceGroupId;
                    $resourceCollectionResourceModule->resource_module_id = $resourceModuleId;
                    $resourceCollectionResourceModule->sequence = $sequence;
                    $resourceCollectionResourceModule->save();
                }
            }
            if ($request->has('resource_collection_ids') && count($request->resource_collection_ids) > 0) {
                $getResourceCollection = ResourceCollectionService::getResourceCollectionBasedOnUUIDArray($request->resource_collection_ids);
                $sequence = ComponentAssociation::where([
                    ['resource_group_id', '=', $resourceGroupId],
                    ['resource_collection_id', '!=', null],
                ])->select('sequence')->orderBy('id', 'desc')->first();
                if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                    $sequence = $sequence->sequence;
                }
                foreach ($getResourceCollection as $resourceCollectionId) {
                    $sequence++;
                    $resourceCollectionResourceModule = new ComponentAssociation();
                    $resourceCollectionResourceModule->resource_group_id = $resourceGroupId;
                    $resourceCollectionResourceModule->resource_collection_id = $resourceCollectionId;
                    $resourceCollectionResourceModule->sequence = $sequence;
                    $resourceCollectionResourceModule->save();
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceGroupAssociation($resource_group_id)
    {
        try {
            $checkExistsComponentAssociation = ComponentAssociation::select('id')->where('resource_group_id', $resource_group_id)->pluck('id');
            if ($checkExistsComponentAssociation) {
                $deleteComponentAssociation = ComponentAssociation::whereIn('id', $checkExistsComponentAssociation)->delete();
                if (!$deleteComponentAssociation) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateResourceGroupComponentAssociation($request, $resourceGroupId)
    {
        try {
            if ($request->has('resource_ids') && count($request->resource_ids) > 0) {
                $getResourceGroupIds = ResourceModuleService::getResourceModuleBasedOnUUIDArray($request->resource_ids);
                $request->merge(['resource_ids' => $getResourceGroupIds]);
                if (count($request->resource_ids) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['resource_group_id', '=', $resourceGroupId],
                        ['resource_module_id', '!=', null],
                    ])->pluck('resource_module_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->resource_ids);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('resource_group_id', $resourceGroupId)->whereIn('resource_module_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->resource_ids, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['resource_group_id', '=', $resourceGroupId],
                        ['resource_module_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                        $sequence = $sequence->sequence;
                    }
                    foreach ($newComponentAssociation as $resourceModuleId) {
                        $sequence++;
                        $resourceGroupResourceModule = new ComponentAssociation();
                        $resourceGroupResourceModule->resource_group_id = $resourceGroupId;
                        $resourceGroupResourceModule->resource_module_id = $resourceModuleId;
                        $resourceGroupResourceModule->sequence = $sequence;
                        $resourceGroupResourceModule->save();
                    }
                }
            }
            if ($request->has('resource_collection_ids') && count($request->resource_collection_ids) > 0) {
                $getResourceCollection = ResourceCollectionService::getResourceCollectionBasedOnUUIDArray($request->resource_collection_ids);

                $request->merge(['resource_collection_ids' => $getResourceCollection]);
                if (count($request->resource_collection_ids) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['resource_group_id', '=', $resourceGroupId],
                        ['resource_collection_id', '!=', null],
                    ])->pluck('resource_collection_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->resource_collection_ids);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('resource_group_id', $resourceGroupId)->whereIn('resource_collection_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->resource_collection_ids, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['resource_group_id', '=', $resourceGroupId],
                        ['resource_collection_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if (isset($sequence->sequence) && !empty($sequence->sequence)) {
                        $sequence = $sequence->sequence;
                    }
                    foreach ($newComponentAssociation as $resourceCollectionId) {
                        $sequence++;
                        $resourceGroupResourceModule = new ComponentAssociation();
                        $resourceGroupResourceModule->resource_group_id = $resourceGroupId;
                        $resourceGroupResourceModule->resource_collection_id = $resourceCollectionId;
                        $resourceGroupResourceModule->sequence = $sequence;
                        $resourceGroupResourceModule->save();
                    }
                }
            }
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function createChallengeComponentAssociation($request, $challengeId)
    {
        try {
            if ($request->has('labs')) {
                $sequence = 1;
                if (count($request->labs) > 0) {
                    foreach ($request->labs as $lab) {
                        $getLabId = LabService::getLabBasedOnUUID($lab);
                        $checkLabAssociation = ComponentAssociation::where('lab_id', $getLabId->id)->whereNotNull('challenge_id')->orderBy('created_at', 'desc')->first();
                        if ($checkLabAssociation) {
                            $sequence = $checkLabAssociation->sequence + 1;
                        }
                        $challengeLabAssociation = new ComponentAssociation();
                        $challengeLabAssociation->lab_id = $getLabId->id;
                        $challengeLabAssociation->challenge_id = $challengeId;
                        $challengeLabAssociation->sequence = $sequence;
                        $challengeLabAssociation->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('lab_programs')) {
                $sequence = 1;
                if (count($request->lab_programs) > 0) {
                    foreach ($request->lab_programs as $lab_program) {
                        $getLabProgramId = LabProgramService::getLabProgramBasedOnUUID($lab_program);
                        $checkLabProgramAssociation = ComponentAssociation::where('lab_program_id', $getLabProgramId->id)->whereNotNull('challenge_id')->orderBy('created_at', 'desc')->first();
                        if ($checkLabProgramAssociation) {
                            $sequence = $checkLabProgramAssociation->sequence + 1;
                        }
                        $challengeLabProgramAssociation = new ComponentAssociation();
                        $challengeLabProgramAssociation->lab_program_id = $getLabProgramId->id;
                        $challengeLabProgramAssociation->challenge_id = $challengeId;
                        $challengeLabProgramAssociation->sequence = $sequence;
                        $challengeLabProgramAssociation->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('resource_modules')) {
                $sequence = 1;
                if (count($request->resource_modules) > 0) {
                    foreach ($request->resource_modules as $resource_module) {
                        $getResourceModuleId = ResourceModuleService::getResourceModuleBasedOnUUID($resource_module);
                        $checkResourceModuleAssociation = ComponentAssociation::where('resource_module_id', $getResourceModuleId->id)->whereNotNull('challenge_id')->orderBy('created_at', 'desc')->first();
                        if ($checkResourceModuleAssociation) {
                            $sequence = $checkResourceModuleAssociation->sequence + 1;
                        }
                        $challengeResourceModuleAssociation = new ComponentAssociation();
                        $challengeResourceModuleAssociation->resource_module_id = $getResourceModuleId->id;
                        $challengeResourceModuleAssociation->challenge_id = $challengeId;
                        $challengeResourceModuleAssociation->sequence = $sequence;
                        $challengeResourceModuleAssociation->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('resource_collections')) {
                $sequence = 1;
                if (count($request->resource_collections) > 0) {
                    foreach ($request->resource_collections as $resource_collection) {
                        $getResourceCollectionId = ResourceCollectionService::getResourceCollectionBasedOnUUID($resource_collection);
                        $checkResourceCollectionAssociation = ComponentAssociation::where('resource_collection_id', $getResourceCollectionId->id)->whereNotNull('challenge_id')->orderBy('created_at', 'desc')->first();
                        if ($checkResourceCollectionAssociation) {
                            $sequence = $checkResourceCollectionAssociation->sequence + 1;
                        }
                        $challengeResourceCollectionAssociation = new ComponentAssociation();
                        $challengeResourceCollectionAssociation->resource_collection_id = $getResourceCollectionId->id;
                        $challengeResourceCollectionAssociation->challenge_id = $challengeId;
                        $challengeResourceCollectionAssociation->sequence = $sequence;
                        $challengeResourceCollectionAssociation->save();
                        $sequence++;
                    }
                }
            }

            if ($request->has('resource_groups')) {
                $sequence = 1;
                if (count($request->resource_groups) > 0) {
                    foreach ($request->resource_groups as $resource_group) {
                        $getResourceGroupId = ResourceGroupService::getResourceGroupBasedOnUUID($resource_group);
                        $checkResourceGroupAssociation = ComponentAssociation::where('resource_group_id', $getResourceGroupId->id)->whereNotNull('challenge_id')->orderBy('created_at', 'desc')->first();
                        if ($checkResourceGroupAssociation) {
                            $sequence = $checkResourceGroupAssociation->sequence + 1;
                        }
                        $challengeResourceGroupAssociation = new ComponentAssociation();
                        $challengeResourceGroupAssociation->resource_group_id = $getResourceGroupId->id;
                        $challengeResourceGroupAssociation->challenge_id = $challengeId;
                        $challengeResourceGroupAssociation->sequence = $sequence;
                        $challengeResourceGroupAssociation->save();
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

    public function updateChallengeComponentAssociation($request, $challengeId)
    {
        try {
            if ($request->has('labs')) {
                $sequence = 1;
                if (count($request->labs) > 0) {
                    $getLabIds = LabService::getLabIdBasedOnUUIDArray($request->labs);
                    $existComponentAssociation = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['lab_id', '!=', null],
                    ])->pluck('lab_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $getLabIds);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('challenge_id', $challengeId)->whereIn('lab_id', $nonExistingIds)->delete();
                    $newComponentAssociations = array_diff($getLabIds, $existComponentAssociation);
                    $sequences = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['lab_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociations as $lab) {
                        $sequence++;
                        $challengeLabAssociation = new ComponentAssociation();
                        $challengeLabAssociation->challenge_id = $challengeId;
                        $challengeLabAssociation->lab_id = $lab;
                        $challengeLabAssociation->sequence = $sequence;
                        $challengeLabAssociation->save();
                    }
                }
            }

            if ($request->has('lab_programs')) {
                $sequence = 1;
                if (count($request->lab_programs) > 0) {
                    $getLabProgramIds = LabProgramService::getLabProgramIdBasedOnUUIDArray($request->lab_programs);
                    $existComponentAssociation = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['lab_program_id', '!=', null],
                    ])->pluck('lab_program_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $getLabProgramIds);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('challenge_id', $challengeId)->whereIn('lab_program_id', $nonExistingIds)->delete();
                    $newComponentAssociations = array_diff($getLabProgramIds, $existComponentAssociation);
                    $sequences = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['lab_program_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociations as $lab_program) {
                        $sequence++;
                        $challengeLabProgramAssociation = new ComponentAssociation();
                        $challengeLabProgramAssociation->challenge_id = $challengeId;
                        $challengeLabProgramAssociation->lab_program_id = $lab_program;
                        $challengeLabProgramAssociation->sequence = $sequence;
                        $challengeLabProgramAssociation->save();
                    }
                }
            }

            if ($request->has('resource_modules')) {
                $sequence = 1;
                if (count($request->resource_modules) > 0) {
                    $getResourceModuleIds = ResourceModuleService::getResourceModuleBasedOnUUIDArray($request->resource_modules);
                    $existComponentAssociation = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['resource_module_id', '!=', null],
                    ])->pluck('resource_module_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $getResourceModuleIds);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('challenge_id', $challengeId)->whereIn('resource_module_id', $nonExistingIds)->delete();
                    $newComponentAssociations = array_diff($getResourceModuleIds, $existComponentAssociation);
                    $sequences = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['resource_module_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociations as $resource_module) {
                        $sequence++;
                        $challengeResourceModuleAssociation = new ComponentAssociation();
                        $challengeResourceModuleAssociation->challenge_id = $challengeId;
                        $challengeResourceModuleAssociation->resource_module_id = $resource_module;
                        $challengeResourceModuleAssociation->sequence = $sequence;
                        $challengeResourceModuleAssociation->save();
                    }
                }
            }

            if ($request->has('resource_collections')) {
                $sequence = 1;
                if (count($request->resource_collections) > 0) {
                    $getResourceCollectionIds = ResourceCollectionService::getResourceCollectionBasedOnUUIDArray($request->resource_collections);
                    $existComponentAssociation = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['resource_collection_id', '!=', null],
                    ])->pluck('resource_collection_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $getResourceCollectionIds);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('challenge_id', $challengeId)->whereIn('resource_collection_id', $nonExistingIds)->delete();
                    $newComponentAssociations = array_diff($getResourceCollectionIds, $existComponentAssociation);
                    $sequences = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['resource_collection_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociations as $resource_collection) {
                        $sequence++;
                        $challengeResourceCollectionAssociation = new ComponentAssociation();
                        $challengeResourceCollectionAssociation->challenge_id = $challengeId;
                        $challengeResourceCollectionAssociation->resource_collection_id = $resource_collection;
                        $challengeResourceCollectionAssociation->sequence = $sequence;
                        $challengeResourceCollectionAssociation->save();
                    }
                }
            }

            if ($request->has('resource_groups')) {
                $sequence = 1;
                if (count($request->resource_groups) > 0) {
                    $getResourceGroupIds = ResourceGroupService::getResourceGroupBasedOnUUIDArray($request->resource_groups);
                    $existComponentAssociation = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['resource_group_id', '!=', null],
                    ])->pluck('resource_group_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $getResourceGroupIds);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('challenge_id', $challengeId)->whereIn('resource_group_id', $nonExistingIds)->delete();
                    $newComponentAssociations = array_diff($getResourceGroupIds, $existComponentAssociation);
                    $sequences = ComponentAssociation::where([
                        ['challenge_id', '=', $challengeId],
                        ['resource_group_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    if ($sequences !== null) {
                        $sequence = $sequences->sequence;
                    } else {
                        $sequence = 0;
                    }
                    foreach ($newComponentAssociations as $resource_group) {
                        $sequence++;
                        $challengeResourceGroupAssociation = new ComponentAssociation();
                        $challengeResourceGroupAssociation->challenge_id = $challengeId;
                        $challengeResourceGroupAssociation->resource_group_id = $resource_group;
                        $challengeResourceGroupAssociation->sequence = $sequence;
                        $challengeResourceGroupAssociation->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function cloneChallengeAssociaton($originalChallengeAssociation, $clonedChallengeId)
    {
        try {
            $originalChallengeAssociation->each(function ($challenge_associated) use ($clonedChallengeId) {
                if ($challenge_associated) {
                    $cloneChallengeAssociaton = $challenge_associated->replicate();
                    $cloneChallengeAssociaton->challenge_id = $clonedChallengeId;
                    $cloneChallengeAssociaton->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchChallengePathIdsBasedOnChallengeId($challengeId)
    {
        try {
            $fetchChallengePathIdsBasedOnChallengeId = ComponentAssociation::where('challenge_id', $challengeId)->whereNotNull('challenge_path_id')->pluck('challenge_path_id');

            return $fetchChallengePathIdsBasedOnChallengeId;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchChallengeIdsBasedOnChallengePathId($challengePathId)
    {
        try {
            $fetchChallengeIdsBasedOnChallengePathId = ComponentAssociation::where('challenge_path_id', $challengePathId)->whereNotNull('challenge_id')->pluck('challenge_id');

            return $fetchChallengeIdsBasedOnChallengePathId;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
