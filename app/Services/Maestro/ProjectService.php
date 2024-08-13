<?php

namespace App\Services\Maestro;

use App\Helpers\UtilityHelper;
use App\Models\Project;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ProjectService
{
    public static function getProjectCounts()
    {
        try {
            return Project::count();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectsList()
    {
        try {
            return Project::where('language', LanguageService::getCurrentLanguage())->latest();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function createProject($request)
    {
        try {
            $projectLanguage = 'en';
            if (!empty($request->challenge_id)) {
                $projectLanguage = self::checkProjectLanguage($request->challenge_id);
            }
            $model = new Project();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $createProject = new Project();
            $createProject->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $createProject->language = $projectLanguage;
            $createProject->user_id = (int) $request->user_id;
            $createProject->title = $request->title;
            $createProject->slug = $slug;
            $createProject->description = $request->description;
            $createProject->challenge_id = (int) $request->challenge_id;
            $createProject->lab_id = (int) $request->lab_id;
            $createProject->category_id = $request->category;
            $createProject->type_id = $request->type;
            $createProject->industry_id = $request->industry;
            $createProject->stage_id = $request->stage;
            $createProject->vertical_id = $request->verticals;
            $createProject->status_id = $request->status;
            $createProject->privacy = $request->privacy;
            if ($createProject->save()) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkProjectLanguage($id)
    {
        try {
            return ChallengeService::getProjectLanguageByChallengeId($id);
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteProject($id)
    {
        try {
            $project = Project::find($id);
            if (!empty($project)) {
                return $project->delete();
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getProjectById($id)
    {
        try {
            $project = Project::findOrFail($id);
            if ($project != null) {
                return $project;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updateProjectById($id, $request)
    {
        try {
            $updateProject = Project::findOrFail($id);
            if (!empty($updateProject)) {
                $projectLanguage = $updateProject->language;
                if (!empty($request->challenge_id)) {
                    $projectLanguage = self::checkProjectLanguage($request->challenge_id);
                }
                $updateProject->user_id = (int) $request->user_id;
                $updateProject->title = $request->title;
                $updateProject->description = $request->description;
                $updateProject->challenge_id = (int) $request->challenge_id;
                $updateProject->language = $projectLanguage;
                $updateProject->lab_id = (int) $request->lab_id;
                $updateProject->category_id = $request->category;
                $updateProject->type_id = $request->type;
                $updateProject->industry_id = $request->industry;
                $updateProject->stage_id = $request->stage;
                $updateProject->vertical_id = $request->verticals;
                $updateProject->status_id = $request->status;
                $updateProject->privacy = $request->privacy;
                if ($updateProject->save()) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getList($getPreSelectedLabTemplates, $language)
    {
        try {
            return Project::whereIn('id', $getPreSelectedLabTemplates)->where('privacy', '0')->where('language', $language)->orderBy('id', 'DESC')->pluck('title', 'id');
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
