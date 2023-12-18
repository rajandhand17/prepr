<?php

namespace App\Services\Manage;

use App\Models\ComponentAssociation;
use App\Models\Lab;
use App\Models\TemplateComponentAssociation;
use Exception;

class ComponentAssociationService
{
    public function labAssociation($request, $lab)
    {
        if ($request->has('lab_programs')) {
            $sequence = 1;
            if (count($request->lab_programs) > 0) {
                foreach ($request->lab_programs as $lab_program) {
                    $labSkillsGroupsStack = new ComponentAssociation();
                    $labSkillsGroupsStack->lab_id = $lab->id;
                    $labSkillsGroupsStack->lab_program_id = $lab_program;
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
                    $labSkillsGroupsStack = new ComponentAssociation();
                    $labSkillsGroupsStack->lab_id = $lab->id;
                    $labSkillsGroupsStack->challenge_id = $challenge;
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
                    $labSkillsGroupsStack = new ComponentAssociation();
                    $labSkillsGroupsStack->lab_id = $lab->id;
                    $labSkillsGroupsStack->challenge_path_id = $challenge_path;
                    $labSkillsGroupsStack->sequence = $sequence;
                    $labSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        if ($request->has('resource_modules')) {
            $sequence = 1;
            if (count($request->resource_modules) > 0) {
                foreach ($request->resource_modules as $resource_module) {
                    $labSkillsGroupsStack = new ComponentAssociation();
                    $labSkillsGroupsStack->lab_id = $lab->id;
                    $labSkillsGroupsStack->resource_module_id = $resource_module;
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
                    $labSkillsGroupsStack = new ComponentAssociation();
                    $labSkillsGroupsStack->lab_id = $lab->id;
                    $labSkillsGroupsStack->resource_group_id = $resource_group;
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
                    $labSkillsGroupsStack = new ComponentAssociation();
                    $labSkillsGroupsStack->lab_id = $lab->id;
                    $labSkillsGroupsStack->resource_collection_id = $resource_collection;
                    $labSkillsGroupsStack->sequence = $sequence;
                    $labSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        return true;
    }

    public function updateLabAssociation($request, $lab_id)
    {
        try {
            if ($request->has('lab_programs')) {
                $sequence = 1;
                if (count($request->lab_programs) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['lab_program_id', '!=', null],
                    ])->pluck('lab_program_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->lab_programs);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('lab_id', $lab_id)->whereIn('lab_program_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->lab_programs, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['lab_program_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first()->sequence;
                    foreach ($newComponentAssociation as $lab_program) {
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
                    $existComponentAssociationchallenge = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['challenge_id', '!=', null],
                    ])->pluck('challenge_id')->all();
                    $nonExistingIdsChallengeId = array_diff($existComponentAssociationchallenge, $request->challenges);
                    $deleteNonExistingComponentAssociationChallenges = ComponentAssociation::where('lab_id', $lab_id)->whereIn('challenge_id', $nonExistingIdsChallengeId)->delete();
                    $newComponentAssociationChallenge = array_diff($request->challenges, $existComponentAssociationchallenge);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['challenge_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first()->sequence;
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
                    $existComponentAssociationChallengePathId = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['challenge_path_id', '!=', null],
                    ])->pluck('challenge_path_id')->all();
                    $nonExistingIdsChallengePathId = array_diff($existComponentAssociationChallengePathId, $request->challenge_paths);
                    $deleteNonExistingComponentAssociationChallengesPath = ComponentAssociation::where('lab_id', $lab_id)->whereIn('challenge_path_id', $nonExistingIdsChallengePathId)->delete();
                    $newComponentAssociationChallengePathId = array_diff($request->challenges, $existComponentAssociationchallenge);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['challenge_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first()->sequence;
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
                    $existComponentAssociationResourceModuleId = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['resource_module_id', '!=', null],
                    ])->pluck('resource_module_id')->all();
                    $nonExistingIdsResourceModuleId = array_diff($existComponentAssociationResourceModuleId, $request->resource_modules);
                    $deleteNonExistingResourceModuleId = ComponentAssociation::where('lab_id', $lab_id)->whereIn('resource_module_id', $nonExistingIdsResourceModuleId)->delete();
                    $newComponentAssociationResourceModuleId = array_diff($request->resource_modules, $existComponentAssociationResourceModuleId);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['resource_module_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first()->sequence;
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

            if ($request->has('resource_groups')) {
                $sequence = 1;
                if (count($request->resource_groups) > 0) {
                    $existComponentAssociationResourceGroupId = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['resource_group_id', '!=', null],
                    ])->pluck('resource_group_id')->all();
                    $nonExistingIdsResourceGroupId = array_diff($existComponentAssociationResourceGroupId, $request->resource_groups);
                    $deleteNonExistingResourceGroupId = ComponentAssociation::where('lab_id', $lab_id)->whereIn('resource_group_id', $nonExistingIdsResourceGroupId)->delete();
                    $newComponentAssociationResourceGroupId = array_diff($request->resource_groups, $existComponentAssociationResourceGroupId);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['resource_group_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first()->sequence;
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

            if ($request->has('resource_collections')) {
                $sequence = 1;
                if (count($request->resource_collections) > 0) {
                    //ResourceCollectionId
                    $existComponentAssociationResourceCollectionId = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['resource_collection_id', '!=', null],
                    ])->pluck('resource_collection_id')->all();
                    $nonExistingIdsResourceCollectionId = array_diff($existComponentAssociationResourceCollectionId, $request->resource_collections);
                    $deleteNonExistingResourceGroupId = ComponentAssociation::where('lab_id', $lab_id)->whereIn('resource_collection_id', $nonExistingIdsResourceCollectionId)->delete();
                    $newComponentAssociationResourceGroupId = array_diff($request->resource_collections, $existComponentAssociationResourceCollectionId);
                    $sequence = ComponentAssociation::where([
                        ['lab_id', '=', $lab_id],
                        ['resource_collection_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first()->sequence;
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

            return true;
        } catch (\Exception $e) {
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
                    $sequence = ComponentAssociation::where([
                        ['lab_program_id', '=', $lab_programs],
                        ['lab_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first()->sequence;
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
                    $newComponentAssociation = array_diff($request->resource_ids, $existComponentAssociation);
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
            return false;
        }
    }

    public function createTemplateLabAssociation($labTemplateId, $lab)
    {
        try {
            $componentAssociation = ComponentAssociation::where('lab_id', $lab->id)->get();
            if ($componentAssociation) {
                foreach ($componentAssociation as $association) {
                    $labSkillsGroupsStack = new TemplateComponentAssociation();
                    $labSkillsGroupsStack->template_lab_id = $labTemplateId->id;
                    $labSkillsGroupsStack->template_lab_program_id = $association->lab_program_id;
                    $labSkillsGroupsStack->template_challenge_id = $association->challenge_id;
                    $labSkillsGroupsStack->template_challenge_path_id = $association->challenge_path_id;
                    $labSkillsGroupsStack->template_resource_module_id = $association->resource_module_id;
                    $labSkillsGroupsStack->template_resource_collection_id = $association->resource_collection_id;
                    $labSkillsGroupsStack->template_resource_group_id = $association->resource_group_id;
                    $labSkillsGroupsStack->sequence = $association->sequence;
                    $labSkillsGroupsStack->save();
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
