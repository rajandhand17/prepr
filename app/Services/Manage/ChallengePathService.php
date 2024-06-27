<?php

namespace App\Services\Manage;

use App\Events\ChallengePath\DeleteChallengePathAssociatedData;
use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\ChallengePath;
use App\Services\AchievementService;
use App\Services\ModuleAchievementCompletionStatusService;
use App\Services\ProjectService;
use App\Services\Public\ChallengePathSocialActivitiesService;
use App\Services\UserService;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ChallengePathService
{
    public function getChallengePathCountBasedOnOrganization($organizationId)
    {
        try {
            $challengePath_count = ChallengePath::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->count();

            return $challengePath_count;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getChallengePathList($request, $organization)
    {
        $getChallengePathList = ChallengePath::where('organization_id', '=', $organization->id);
        $getChallengePathList = self::filterChallengePathList($getChallengePathList, $request);

        return $getChallengePathList->paginate(config('site-settings.pagination_per_page'));
    }

    public function filterChallengePathList($getChallengePathList, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $getChallengePathList = $getChallengePathList->where('challenge_paths.title', 'like', '%'.$request->search.'%');
            }
            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $getChallengePathList = $getChallengePathList->whereIn('challenge_paths.category_id', $request->category);
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $challengeIds = ChallengePathSocialActivitiesService::getChallengePathsBasedOnActivity($activityType)->pluck('challenge_path_id');
                $getChallengePathList->whereIn('challenge_paths.id', $challengeIds);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $getChallengePathList->orderBy('challenge_paths.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $getChallengePathList->orderBy('challenge_paths.title', 'DESC');
                        break;
                    case 'creation_date':
                        $getChallengePathList->orderBy('challenge_paths.created_at', 'ASC');
                        break;
                    default:
                        $getChallengePathList->orderBy('challenge_paths.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $getChallengePathList = $getChallengePathList->where('challenge_paths.privacy', '0');
                        break;
                    case 'private':
                        $getChallengePathList = $getChallengePathList->where('challenge_paths.privacy', '1');
                        break;
                    default:
                        $getChallengePathList = $getChallengePathList;
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $getChallengePathList = $getChallengePathList->whereIn('challenge_paths.id', function ($query) use ($request) {
                    $query->select('challenge_path_skill_group_stacks.challenge_path_id')
                    ->from('challenge_path_skill_group_stacks')
                    ->whereIn('challenge_path_skill_group_stacks.foreign_id', $request->skills)
                        ->where('challenge_path_skill_group_stacks.type', '0')
                        ->whereNull('challenge_path_skill_group_stacks.deleted_at')
                        ->distinct();
                })->distinct('challenge_paths.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $getChallengePathList = $getChallengePathList->whereIn('challenge_paths.id', function ($query) use ($request) {
                    $query->select('challenge_path_tag_groups.challenge_path_id')
                    ->from('challenge_path_tag_groups')
                    ->whereIn('challenge_path_tag_groups.foreign_id', $request->tags)
                        ->where('challenge_path_tag_groups.type', '0')
                        ->whereNull('challenge_path_tag_groups.deleted_at')
                        ->distinct();
                })->distinct('challenge_paths.uuid');
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $getChallengePathList = $getChallengePathList->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $getChallengePathList = $getChallengePathList->whereIn('level_id', $request->level_id);
            }

            return $getChallengePathList;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function uploadChallengePathMedia($image)
    {
        try {
            $upload_challenge_path_cover_image = FileUploadHelper::uploadImageToS3($image, 'challenge_path');
            if ($upload_challenge_path_cover_image == false) {
                return false;
            }

            return $upload_challenge_path_cover_image;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createChallengePath($cover_image, $request, $organizationId)
    {
        try {
            $privacy = config('constants.challenge_privacy.no');
            switch ($request->privacy) {
                case 'yes':
                    $privacy = config('constants.challenge_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.challenge_privacy.no');
                    break;
                default:
                    $privacy = config('constants.challenge_privacy.yes');
                    break;
            }

            $status = config('constants.challenge_status.draft');
            switch ($request->status) {
                case 'draft':
                    $status = config('constants.challenge_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.challenge_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.challenge_status.archive');
                    break;
                default:
                    $status = config('constants.challenge_status.draft');
                    break;
            }

            $is_achievement_enabled = config('constants.challenge_achievement_enable.no');
            switch ($request->is_achievement_enabled) {
                case 'yes':
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.yes');
                    break;
                case 'no':
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.no');
                    break;
                default:
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.yes');
                    break;
            }

            $is_auto_created = config('constants.challenge_auto_created.no');
            switch ($request->is_auto_created) {
                case 'yes':
                    $is_auto_created = config('constants.challenge_auto_created.yes');
                    break;
                case 'no':
                    $is_auto_created = config('constants.challenge_auto_created.no');
                    break;
                default:
                    $is_auto_created = config('constants.challenge_auto_created.yes');
                    break;
            }

            $is_sequential = config('constants.challenge_sequential.no');
            switch ($request->is_sequential) {
                case 'yes':
                    $is_sequential = config('constants.challenge_sequential.yes');
                    break;
                case 'no':
                    $is_sequential = config('constants.challenge_sequential.no');
                    break;
                default:
                    $is_sequential = config('constants.challenge_sequential.yes');
                    break;
            }

            $model = new ChallengePath();
            $slug = UtilityHelper::generateSlug($request->title, $model);

            $challengePath = new ChallengePath();
            $challengePath->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challengePath->language = $request->language;
            $challengePath->slug = $slug;
            $challengePath->title = $request->title;
            $challengePath->description = $request->description;
            $challengePath->user_id = auth()->user()->id;
            $challengePath->organization_id = $organizationId;
            $challengePath->category_id = $request->category_id;
            $challengePath->duration_id = $request->duration_id;
            $challengePath->level_id = $request->level_id;
            $challengePath->media_type = 'image';
            $challengePath->media = $cover_image;
            $challengePath->privacy = $privacy;
            $challengePath->status = $status;
            $challengePath->is_achievement_enabled = $is_achievement_enabled;
            $challengePath->is_sequential = $is_sequential;
            $challengePath->is_auto_created = $is_auto_created;
            $challengePath->save();

            return $challengePath;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateChallengePath($slug, $request, $upload_cover_image, $organizationId)
    {
        try {
            $challengePath = ChallengePath::where('slug', $slug)->first();
            $privacy = $challengePath->privacy;
            switch ($request->privacy) {
                case 'yes':
                    $privacy = config('constants.challenge_privacy.yes');
                    break;
                case 'no':
                    $privacy = config('constants.challenge_privacy.no');
                    break;
                default:
                    $privacy = config('constants.challenge_privacy.yes');
                    break;
            }

            $status = $challengePath->status;
            switch ($request->status) {
                case 'draft':
                    $status = config('constants.challenge_status.draft');
                    break;
                case 'publish':
                    $status = config('constants.challenge_status.publish');
                    break;
                case 'archive':
                    $status = config('constants.challenge_status.archive');
                    break;
                default:
                    $status = config('constants.challenge_status.draft');
                    break;
            }

            $is_achievement_enabled = $challengePath->is_achievement_enabled;
            switch ($request->is_achievement_enabled) {
                case 'yes':
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.yes');
                    break;
                case 'no':
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.no');
                    break;
                default:
                    $is_achievement_enabled = config('constants.challenge_achievement_enable.yes');
                    break;
            }

            $is_auto_created = $challengePath->is_auto_created;
            switch ($request->is_auto_created) {
                case 'yes':
                    $is_auto_created = config('constants.challenge_auto_created.yes');
                    break;
                case 'no':
                    $is_auto_created = config('constants.challenge_auto_created.no');
                    break;
                default:
                    $is_auto_created = config('constants.challenge_auto_created.yes');
                    break;
            }

            $is_sequential = $challengePath->is_sequential;
            switch ($request->is_sequential) {
                case 'yes':
                    $is_sequential = config('constants.challenge_sequential.yes');
                    break;
                case 'no':
                    $is_sequential = config('constants.challenge_sequential.no');
                    break;
                default:
                    $is_sequential = config('constants.challenge_sequential.yes');
                    break;
            }

            $challengePath->language = ($request->has('language')) ? $request->language : $challengePath->language;
            $challengePath->title = ($request->has('title')) ? $request->title : $challengePath->title;
            $challengePath->description = ($request->has('description')) ? $request->description : $challengePath->description;
            $challengePath->organization_id = $organizationId;
            $challengePath->category_id = ($request->has('category_id')) ? $request->category_id : $challengePath->category_id;
            $challengePath->duration_id = ($request->has('duration_id')) ? $request->duration_id : $challengePath->duration_id;
            $challengePath->level_id = ($request->has('level_id')) ? $request->level_id : $challengePath->level_id;
            $challengePath->media = ($upload_cover_image) ? $upload_cover_image : $challengePath->media;
            $challengePath->privacy = $privacy;
            $challengePath->status = $status;
            $challengePath->is_auto_created = $is_auto_created;
            $challengePath->is_sequential = $is_sequential;
            $challengePath->is_achievement_enabled = $is_achievement_enabled;
            $challengePath->save();

            return $challengePath;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkSlug($slug)
    {
        try {
            return ChallengePath::where('slug', $slug)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $checkChallengePathName = ChallengePath::where('title', $title)->first();
            if ($checkChallengePathName) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function delete($challengePathId)
    {
        try {
            $challengePath = ChallengePath::find($challengePathId)->delete();
            if ($challengePath) {
                $deleteAssociatedData = event(new DeleteChallengePathAssociatedData($challengePathId));

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengePathBasedOnId($id)
    {
        try {
            return ChallengePath::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getChallengePathListName($request, $organization)
    {
        try {
            $challengePathList = ChallengePath::select('uuid', 'title', 'media')->where(['organization_id' => $organization->id, 'is_accessible' => '1']);
            $challengePathList = self::filterChallengePathList($challengePathList, $request);
            $limit = config('site-settings.listing_limit');

            return $challengePathList->limit($limit)->get();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengePathBasedOnUUID($uuid)
    {
        try {
            return ChallengePath::where('UUID', $uuid)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengePathBasedOnUUIDArray($challengePathUUIDArray)
    {
        try {
            $challengePathIds = ChallengePath::whereIn('uuid', $challengePathUUIDArray)->pluck('id')->all();
            if ($challengePathIds != null) {
                return $challengePathIds;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteOrganizationChallengePath($organizationId)
    {
        try {
            $fetchOrganizationChallengePaths = ChallengePath::where('organization_id', $organizationId)->pluck('id');
            if (!empty($fetchOrganizationChallengePaths)) {
                foreach ($fetchOrganizationChallengePaths as $organizationChallengePath) {
                    $deleteOrganizationChallengePath = self::delete($organizationChallengePath);
                    if (!$deleteOrganizationChallengePath) {
                        return false;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengePathAchievementStatus($memberIds, $challengeId)
    {
        try {
            if (!empty($memberIds)) {
                foreach ($memberIds as $memberId) {
                    $getUserById = UserService::getUserById($memberId);
                    $fetchChallengePathIdsBasedOnChallengeId = ComponentAssociationService::fetchChallengePathIdsBasedOnChallengeId($challengeId);
                    if (!empty($fetchChallengePathIdsBasedOnChallengeId)) {
                        foreach ($fetchChallengePathIdsBasedOnChallengeId as $challengePathId) {
                            $checkChallengePathAchievementAssignedOrNot = ModuleAchievementCompletionStatusService::checkChallengePathAchievementAssignedOrNot($challengePathId, $getUserById->id);
                            if ($checkChallengePathAchievementAssignedOrNot === false) {
                                $fetchChallengeIdsBasedOnChallengePathId = ComponentAssociationService::fetchChallengeIdsBasedOnChallengePathId($challengePathId);
                                if (!empty($fetchChallengeIdsBasedOnChallengePathId)) {
                                    $fetchSubmittedProjectsChallengeId = ProjectService::fetchSubmittedProjectsChallengeId($getUserById);
                                    $checkChallengeDiff = $fetchChallengeIdsBasedOnChallengePathId->diff($fetchSubmittedProjectsChallengeId);
                                    if ($checkChallengeDiff->isEmpty()) {
                                        $addChallengePathAchievement = AchievementService::addChallengePathAchievement($challengePathId, $getUserById->id);
                                        if ($addChallengePathAchievement) {
                                            $markChallengePathCompleted = ModuleAchievementCompletionStatusService::markChallengePathCompleted($challengePathId, $getUserById->id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
