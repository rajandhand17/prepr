<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\ChallengePath;
use Exception;

class ChallengePathService
{
    public function getList($request)
    {
        try {
            $challengePathList = ChallengePath::where('is_accessible', '1');
            $challengePathList = self::filterChallengePathList($request, $challengePathList);

            return $challengePathList->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function filterChallengePathList($request, $challengePathList)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $challengePathList = $challengePathList->whereSearchFilter($request->search ?? '');
            }
            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $challengePathList = $challengePathList->whereIn('challenge_paths.category_id', $request->category);
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                $challengePathList = $challengePathList->whereIn('organization_id', $getOrganizationIds);
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $challengeIds = ChallengePathSocialActivitiesService::getChallengePathsBasedOnActivity($activityType)->pluck('challenge_path_id');
                $challengePathList->whereIn('challenge_paths.id', $challengeIds);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $challengePathList->orderBy('challenge_paths.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $challengePathList->orderBy('challenge_paths.title', 'DESC');
                        break;
                    case 'creation_date':
                        $challengePathList->orderBy('challenge_paths.created_at', 'ASC');
                        break;
                    default:
                        $challengePathList->orderBy('challenge_paths.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $challengePathList = $challengePathList->where('challenge_paths.privacy', '0');
                        break;
                    case 'private':
                        $challengePathList = $challengePathList->where('challenge_paths.privacy', '1');
                        break;
                    default:
                        $challengePathList = [];
                        break;
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $challengePathList = $challengePathList->whereIn('challenge_paths.id', function ($query) use ($request) {
                    $query->select('challenge_path_skill_group_stacks.challenge_path_id')
                        ->from('challenge_path_skill_group_stacks')
                        ->whereIn('challenge_path_skill_group_stacks.foreign_id', $request->skills)
                        ->where('challenge_path_skill_group_stacks.type', '0')
                        ->whereNull('challenge_path_skill_group_stacks.deleted_at')
                        ->distinct();
                })->distinct('challenge_paths.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $challengePathList = $challengePathList->whereIn('challenge_paths.id', function ($query) use ($request) {
                    $query->select('challenge_path_tag_groups.challenge_path_id')
                        ->from('challenge_path_tag_groups')
                        ->whereIn('challenge_path_tag_groups.foreign_id', $request->tags)
                        ->where('challenge_path_tag_groups.type', '0')
                        ->whereNull('challenge_path_tag_groups.deleted_at')
                        ->distinct();
                })->distinct('challenge_paths.uuid');
            }

            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $challengePathList = $challengePathList->whereIn('duration_id', $request->duration_id);
            }

            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $challengePathList = $challengePathList->whereIn('level_id', $request->level_id);
            }

            if ($request->has('type') && $request->type) {
                $challengePathList = $challengePathList->whereHas('challenge_path_type', function ($query) use ($request) {
                    $query->where('value', config('constants.resource_types.'.$request->type));
                });
            }

            return $challengePathList;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getChallengePathBasedOnSlug($slug)
    {
        try {
            return ChallengePath::where('slug', $slug)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengePathBasedOnId($id)
    {
        try {
            return ChallengePath::where('id', $id)->first();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getChallengePathBasedOnArrayIds($ids)
    {
        try {
            return ChallengePath::whereIn('id', $ids)->where('is_accessible', '1')->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getComponentBasedChallengePathList($request, $organizationId)
    {
        try {
            $challengePathList = ChallengePath::where(['organization_id' => $organizationId, 'status' => '1', 'is_accessible' => '1']);
            $challengePathList = self::filterChallengePathList($request, $challengePathList);

            return $challengePathList->paginate(config('site-settings.association_pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchChallengePathAssociation($request, $fetchChallengePathIdsAssociatedLabId)
    {
        try {
            $challengePathList = ChallengePath::whereIn('id', $fetchChallengePathIdsAssociatedLabId)->where(['status' => '1', 'is_accessible' => '1']);
            $challengePathList = self::filterChallengePathList($request, $challengePathList);

            return $challengePathList->paginate(config('site-settings.association_pagination_per_page'));
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getRelatedChallengePaths($challengePathIds)
    {
        try {
            // Retrieve challenge path with the given IDs using findMany for primary keys and limiting by 2 values
            $challengePaths = ChallengePath::findMany($challengePathIds)->slice(0, 2);

            return $challengePaths;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
