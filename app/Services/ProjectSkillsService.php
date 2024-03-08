<?php

namespace App\Services;

use App\Models\ProjectSkill;
use Exception;

class ProjectSkillsService
{
    public function addUpdateProjectSkills($projectId, $request)
    {
        try {
            if ($request->has('skills')) {
                if (count($request->skills) > 0) {
                    $getExistsSkills = ProjectSkill::where([
                        ['project_id', '=', $projectId],
                    ])->pluck('skill_id')->toArray();
                    $nonExistingIds = array_diff($getExistsSkills, $request->skills);
                    $deleteNonExistingSkills = ProjectSkill::where([
                        ['project_id', '=', $projectId],
                    ])->whereIn('skill_id', $nonExistingIds)->delete();
                    $newSkills = array_diff($request->skills, $getExistsSkills);
                    foreach ($newSkills as $skill) {
                        $projectSkills = new ProjectSkill();
                        $projectSkills->project_id = $projectId;
                        $projectSkills->skill_id = $skill;
                        $projectSkills->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}