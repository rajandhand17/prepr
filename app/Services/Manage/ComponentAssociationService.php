<?php

namespace App\Services\Manage;

use App\Models\ComponentAssociation;
use App\Models\Lab;

class ComponentAssociationService
{
    public function labAssociation($request, $lab)
    {
        if ($request->has('lab_programs')) {
            $sequence = 1;
            if (count($request->lab_programs) > 0) {
                foreach ($request->lab_programs as $lab_program) {
                    $LabSkillsGroupsStack = new ComponentAssociation();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->lab_program_id = $lab_program;
                    $LabSkillsGroupsStack->sequence = $sequence;
                    $LabSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        if ($request->has('challenges')) {
            $sequence = 1;
            if (count($request->challenges) > 0) {
                foreach ($request->challenges as $challenge) {
                    $LabSkillsGroupsStack = new ComponentAssociation();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->challenge_id = $challenge;
                    $LabSkillsGroupsStack->sequence = $sequence;
                    $LabSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        if ($request->has('challenge_paths')) {
            $sequence = 1;
            if (count($request->challenge_paths) > 0) {
                foreach ($request->challenge_paths as $challenge_path) {
                    $LabSkillsGroupsStack = new ComponentAssociation();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->challenge_path_id = $challenge_path;
                    $LabSkillsGroupsStack->sequence = $sequence;
                    $LabSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        if ($request->has('resource_modules')) {
            $sequence = 1;
            if (count($request->resource_modules) > 0) {
                foreach ($request->resource_modules as $resource_module) {
                    $LabSkillsGroupsStack = new ComponentAssociation();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->resource_module_id = $resource_module;
                    $LabSkillsGroupsStack->sequence = $sequence;
                    $LabSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        if ($request->has('resource_groups')) {
            $sequence = 1;
            if (count($request->resource_groups) > 0) {
                foreach ($request->resource_groups as $resource_group) {
                    $LabSkillsGroupsStack = new ComponentAssociation();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->resource_group_id = $resource_group;
                    $LabSkillsGroupsStack->sequence = $sequence;
                    $LabSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        if ($request->has('resource_collections')) {
            $sequence = 1;
            if (count($request->resource_collections) > 0) {
                foreach ($request->resource_collections as $resource_collection) {
                    $LabSkillsGroupsStack = new ComponentAssociation();
                    $LabSkillsGroupsStack->lab_id = $lab->id;
                    $LabSkillsGroupsStack->resource_collection_id = $resource_collection;
                    $LabSkillsGroupsStack->sequence = $sequence;
                    $LabSkillsGroupsStack->save();
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
                        $LabSkillsGroupsStack = new ComponentAssociation();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->lab_program_id = $lab_program;
                        $LabSkillsGroupsStack->sequence = $sequence;
                        $LabSkillsGroupsStack->save();
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
                        $LabSkillsGroupsStack = new ComponentAssociation();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->challenge_id = $challenge;
                        $LabSkillsGroupsStack->sequence = $sequence;
                        $LabSkillsGroupsStack->save();
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
                        $LabSkillsGroupsStack = new ComponentAssociation();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->challenge_path_id = $challenge_path;
                        $LabSkillsGroupsStack->sequence = $sequence;
                        $LabSkillsGroupsStack->save();
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
                        $LabSkillsGroupsStack = new ComponentAssociation();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->resource_module_id = $resource_module;
                        $LabSkillsGroupsStack->sequence = $sequence;
                        $LabSkillsGroupsStack->save();
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
                        $LabSkillsGroupsStack = new ComponentAssociation();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->resource_group_id = $resource_group;
                        $LabSkillsGroupsStack->sequence = $sequence;
                        $LabSkillsGroupsStack->save();
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
                        $LabSkillsGroupsStack = new ComponentAssociation();
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->resource_collection_id = $resource_collection;
                        $LabSkillsGroupsStack->sequence = $sequence;
                        $LabSkillsGroupsStack->save();
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

                return true;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function labProgramAssociation($request, $labProgram)
    {
        if ($request->has('lab_id')) {
            $sequence = 1;
            if (count($request->lab_id) > 0) {
                foreach ($request->lab_id as $lab) {
                    $lab_id = Lab::where('uuid', $lab)->select('id')->first()->id;
                    $LabSkillsGroupsStack = new ComponentAssociation();
                    $LabSkillsGroupsStack->lab_id = $lab_id;
                    $LabSkillsGroupsStack->lab_program_id = $labProgram->id;
                    $LabSkillsGroupsStack->sequence = $sequence;
                    $LabSkillsGroupsStack->save();
                    $sequence++;
                }
            }
        }

        return true;
    }

    public function updateLabProgramAssociation($request, $lab_programs)
    {
        try {
            if ($request->has('lab_id')) {
                $sequence = 1;
                $getLabId = LabService::getLabIdBasedOnUUIDArray($request->lab_id);
                $request->merge(['lab_id' => $getLabId]);
                if (count($request->lab_id) > 0) {
                    $existComponentAssociation = ComponentAssociation::where([
                        ['lab_program_id', '=', $lab_programs],
                        ['lab_id', '!=', null],
                    ])->pluck('lab_id')->all();
                    $nonExistingIds = array_diff($existComponentAssociation, $request->lab_id);
                    $deleteNonExistingComponentAssociation = ComponentAssociation::where('lab_program_id', $lab_programs)->whereIn('lab_id', $nonExistingIds)->delete();
                    $newComponentAssociation = array_diff($request->lab_id, $existComponentAssociation);
                    $sequence = ComponentAssociation::where([
                        ['lab_program_id', '=', $lab_programs],
                        ['lab_id', '!=', null],
                    ])->select('sequence')->orderBy('id', 'desc')->first();
                    foreach ($newComponentAssociation as $lab_id) {
                        $sequence++;
                        $LabSkillsGroupsStack = new ComponentAssociation();
                        $LabSkillsGroupsStack->lab_program_id = $lab_programs;
                        $LabSkillsGroupsStack->lab_id = $lab_id;
                        $LabSkillsGroupsStack->sequence = $sequence;
                        $LabSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
