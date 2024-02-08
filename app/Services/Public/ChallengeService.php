<?php

namespace App\Services\Public;

use App\Models\Challenge;

class ChallengeService
{
    public function getList($request)
    {
        try {
            $challenge_list = Challenge::select()->where('challenges.status', '1');
            $challenge_list = self::filterChallengeList($request, $challenge_list);

            return $challenge_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterChallengeList($request, $challenge_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $challenge_list = $challenge_list->where('challenges.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'deactivated') ? '2' : '3'));
                $challenge_list = $challenge_list->where('challenges.status', $status);
            } else {
                $challenge_list = $challenge_list->where('challenges.status', '1');
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $challenge_list = $challenge_list->whereIn('challenges.category_id', $request->category);
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                if (!empty($getOrganizationIds)) {
                    $challenge_list = $challenge_list->whereIn('organization_id', $getOrganizationIds);
                }
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $challengeIds = ChallengeSocialActivitiesService::getChallengeBasedOnActivity($activityType)->pluck('challenge_id');
                $challenge_list->whereIn('challenges.id', $challengeIds);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $challenge_list->orderBy('challenges.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $challenge_list->orderBy('challenges.title', 'DESC');
                        break;
                    case 'creation_date':
                        $challenge_list->orderBy('challenges.created_at', 'ASC');
                        break;
                    default:
                        $challenge_list->orderBy('challenges.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $challenge_list = $challenge_list->where('challenges.privacy', '0');
                        break;
                    case 'private':
                        $challenge_list = $challenge_list->where('challenges.privacy', '1');
                        break;
                    default:
                        $challenge_list = $challenge_list;
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $challenge_list = $challenge_list->whereIn('challenges.id', function ($query) use ($request) {
                    $query->select('challenge_skills_groups_stacks.challenge_id')
                    ->from('challenge_skills_groups_stacks')
                    ->whereIn('challenge_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('challenge_skills_groups_stacks.type', '0')
                        ->whereNull('challenge_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('challenges.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $challenge_list = $challenge_list->whereIn('challenges.id', function ($query) use ($request) {
                    $query->select('challenge_tags_groups.challenge_id')
                    ->from('challenge_tags_groups')
                    ->whereIn('challenge_tags_groups.foreign_id', $request->tags)
                        ->where('challenge_tags_groups.type', '0')
                        ->whereNull('challenge_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('challenges.uuid');
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $challenge_list = $challenge_list->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('request_status') && !empty($request->request_status)) {
                if (auth('api')->check()) {
                    $status_array = ['accepted', 'pending', 'declined'];
                    if (in_array($request->request_status, $status_array)) {
                        $challenge_list = $challenge_list->join('member_management', 'challenges.id', '=', 'member_management.module_id')
                        ->where(['member_management.module_type' => '2', 'member_management.email' => auth('api')->user()->email]);
                        switch ($request->request_status) {
                            case 'accepted':
                                $challenge_list->where('member_management.invite_status', '1');
                                break;
                            case 'pending':
                                $challenge_list->where('member_management.invite_status', '2');
                                break;
                            case 'declined':
                                $challenge_list->where('member_management.invite_status', '3');
                                break;
                            default:
                                $challenge_list;
                        }
                    }
                }
            }

            return $challenge_list;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getChallengeBasedOnSlug($slug)
    {
        try {
            return Challenge::where('slug', $slug)->first();
        } catch (\Exception $e) {
            return false;
        }
    }
}
