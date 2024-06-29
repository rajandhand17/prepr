<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ProjectSkill;
use App\Models\Skill;
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
                        $projectSkill = Skill::find($skill);
                        $activity = auth()->user()->full_name.' '.__('responses.project_updated_skills').' '.$projectSkill->title;
                        ProjectHistoryService::storeHistory($projectId, auth()->user()->id, $activity);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getProjectIdsBasedOnSkills($skillIds)
    {
        try {
            $projectIds = ProjectSkill::whereIn('skill_id', $skillIds)->pluck('project_id');

            return $projectIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
