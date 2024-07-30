<?php

namespace App\Services\Maestro;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ChallengeService
{
    public static function getChallengeCounts()
    {
        try {
            return Challenge::count();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeList()
    {
        try {
            return Challenge::where('language', LanguageService::getCurrentLanguage())->latest();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function uploadBannerImage($request)
    {
        try {
            $achievementImage = null;
            if ($request->file('cover_image')) {
                $achievementImage = FileUploadHelper::uploadImageToS3($request->file('cover_image'), 'challenge');
            }

            return $achievementImage;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeAssociatedItemsById($challenge)
    {
        try {
            $skillIds = ChallengeSkillsGroupsStackService::getPluckSkillGroupStack($challenge);
            $labIds = ComponentAssociationService::getChallengeAssociatedLab($challenge);
            $moduleIds = ComponentAssociationService::getChallengeAssociatedResourceModule($challenge);
            $organization = !empty($challenge->organization_id) ? OrganizationService::getOrganizationById($challenge->organization_id) : null;
            $category = !empty($challenge->category_id) ? CategoryService::getCategoriesById($challenge->category_id) : null;
            $level = !empty($challenge->level_id) ? LevelsService::getLevelById($challenge->level_id) : null;
            $duration = !empty($challenge->duration_id) ? DurationService::getLevelById($challenge->duration_id) : null;
            $user = !empty($challenge->user_id) ? UserService::getUserPluckById($challenge->user_id) : null;
            $skills = !empty($challenge->id) ? SkillService::getSkillsById($skillIds) : null;
            $labs = !empty($challenge->user_id) ? LabService::getLab('edit', $labIds) : null;
            $resourceModules = !empty($challenge->user_id) ? ResourceModuleService::getResourceModulesByIds($moduleIds) : null;

            return ['category' => $category ?? [], 'organization' => $organization ?? [], 'skills' => $skills ?? [], 'skillIds' => $skillIds ?? [], 'user' => $user ?? [], 'level' => $level ?? [], 'associatedLabs' => $labs ?? [],  'duration' => $duration ?? [], 'labIds' => $labIds ?? [], 'moduleIds' => $moduleIds ?? [], 'resourceModules' => $resourceModules ?? []];
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createChallenge($request)
    {
        try {
            $model = new Challenge();
            $slug = UtilityHelper::generateSlug($request->title, $model);
            $challenge = new Challenge();
            $challenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challenge->language = $request->language;
            $challenge->title = $request->title;
            $challenge->slug = $slug;
            $challenge->user_id = $request->user_id;
            $challenge->organization_id = $request->organization_id;
            $challenge->category_id = $request->category;
            $challenge->duration_id = $request->duration;
            $challenge->level_id = $request->level;
            $challenge->description = $request->description;
            $challenge->is_open = $request->is_open;
            $challenge->status = $request->status;
            $challenge->media_type = 'image';
            $challenge->media = self::uploadBannerImage($request);
            $challenge->status = $request->status;
            $challenge->agreement = ($request->has('agreement')) ? $request->agreement : 'No Terms and Conditions.';
            $challenge->project_privacy = $request->project_privacy;
            if ($challenge->save()) {
                return $challenge;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteChallenge($id)
    {
        try {
            $challenge = Challenge::find($id);
            if (!empty($challenge)) {
                return $challenge->delete();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeById($id)
    {
        try {
            $challenge = Challenge::findOrFail($id);
            if ($challenge) {
                return $challenge;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateChallengeById($id, $request)
    {
        try {
            $challenge = Challenge::find($id);
            if (!empty($challenge)) {
                if ($request->file('cover_image')) {
                    $coverImage = self::uploadBannerImage($request);
                } else {
                    $coverImage = $challenge->media;
                }
                $challenge->title = $request->title;
                $challenge->user_id = $request->user_id;
                $challenge->organization_id = $request->organization_id;
                $challenge->category_id = $request->category;
                $challenge->duration_id = $request->duration;
                $challenge->level_id = $request->level;
                $challenge->description = $request->description;
                $challenge->is_open = $request->is_open;
                $challenge->status = $request->status;
                $challenge->media_type = 'image';
                $challenge->media = $coverImage;
                $challenge->status = $request->status;
                $challenge->agreement = ($request->has('agreement')) ? $request->agreement : 'No Terms and Conditions.';
                $challenge->project_privacy = $request->project_privacy;
                if ($challenge->save()) {
                    return $challenge;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallenges($request)
    {
        try {
            $challenge = Challenge::select('id', 'title')->orderBy('id', 'DESC');
            if ($request->search) {
                $challenge = $challenge->where('title', 'LIKE', '%'.$request->search.'%');
            }
            $challenge = $challenge->get()->take(20)->pluck('title', 'id');
            $count = 0;
            $json_stacks = $json_result = [];
            foreach ($challenge as $key => $challenge_to_return) {
                $json_stacks[$count]['id'] = $key;
                $json_stacks[$count]['text'] = $challenge_to_return;
                $count++;
            }
            $json_result['result'] = $json_stacks;

            return response()->json($json_result);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallenge($action, $challengeId)
    {
        try {
            $challenge = Challenge::select('title', 'id');
            if ($action == 'edit') {
                $challenge = $challenge->where(['id' => $challengeId]);
            }

            return $challenge->pluck('title', 'id');
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getChallengeBasedOnId($id)
    {
        try {
            return Challenge::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengeBasedOnSlug($slug)
    {
        try {
            return Challenge::where(['slug' => $slug, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function updatePreBuilt($challengeId, $is_pre_built)
    {
        try {
            $challengeUpdate = Challenge::find($challengeId);
            $challengeUpdate->is_pre_built = $is_pre_built;
            $challengeUpdate->save();

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
