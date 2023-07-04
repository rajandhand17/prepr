<?php

namespace App\Services;

use App\Models\ComponentAssociation;

class ComponentAssociationService
{
    public function labAssociation($request, $lab)
    {
        try {
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
        } catch (\Exception $e) {
            return false;
        }
    }
}
