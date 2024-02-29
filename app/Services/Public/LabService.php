<?php

namespace App\Services\Public;

use App\Models\Lab;
use App\Models\LabSkillsGroupsStack;
use App\Models\LabTagsGroups;
use App\Services\LabTagGroupService;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;

class LabService
{
    public function getList($request)
    {
        try {
            $lab_list = Lab::select()->where('labs.status', '1');
            $lab_list = self::filterLabList($request, $lab_list);

            return $lab_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function filterLabList($request, $lab_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $lab_list = $lab_list->where('labs.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $lab_list = $lab_list->whereIn('labs.category_id', $request->category);
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                if (!empty($getOrganizationIds)) {
                    $lab_list = $lab_list->whereIn('organization_id', $getOrganizationIds);
                }
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $labIds = LabSocialActivitiesService::getLabsBasedOnActivity($activityType)->pluck('lab_id');
                $lab_list->whereIn('labs.id', $labIds);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $lab_list->orderBy('labs.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $lab_list->orderBy('labs.title', 'DESC');
                        break;
                    case 'creation_date':
                        $lab_list->orderBy('labs.created_at', 'ASC');
                        break;
                    default:
                        $lab_list->orderBy('labs.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $lab_list = $lab_list->where('labs.privacy', '0');
                        break;
                    case 'private':
                        $lab_list = $lab_list->where('labs.privacy', '1');
                        break;
                    default:
                        $lab_list = $lab_list;
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $lab_list = $lab_list->whereIn('labs.id', function ($query) use ($request) {
                    $query->select('lab_skills_groups_stack.lab_id')
                    ->from('lab_skills_groups_stack')
                    ->whereIn('lab_skills_groups_stack.foreign_id', $request->skills)
                        ->where('lab_skills_groups_stack.type', '0')
                        ->whereNull('lab_skills_groups_stack.deleted_at')
                        ->distinct();
                })->distinct('labs.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $lab_list = $lab_list->whereIn('labs.id', function ($query) use ($request) {
                    $query->select('lab_tags_groups.lab_id')
                    ->from('lab_tags_groups')
                    ->whereIn('lab_tags_groups.foreign_id', $request->tags)
                        ->where('lab_tags_groups.type', '0')
                        ->whereNull('lab_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('labs.uuid');
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $lab_list = $lab_list->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $lab_list = $lab_list->whereIn('level_id', $request->level_id);
            }
            if ($request->has('request_status') && !empty($request->request_status)) {
                if (auth('api')->check()) {
                    $status_array = ['accepted', 'pending', 'declined'];
                    if (in_array($request->request_status, $status_array)) {
                        $lab_list = $lab_list->join('member_management', 'labs.id', '=', 'member_management.module_id')
                        ->where(['member_management.module_type' => '1', 'member_management.email' => auth('api')->user()->email]);
                        switch ($request->request_status) {
                            case 'invited':
                                $lab_list->where('member_management.invite_status', '0');
                                break;
                            case 'accepted':
                                $lab_list->where('member_management.invite_status', '1');
                                break;
                            case 'pending':
                                $lab_list->where('member_management.invite_status', '2');
                                break;
                            case 'declined':
                                $lab_list->where('member_management.invite_status', '3');
                                break;
                            default:
                                $lab_list;
                        }
                    }
                }
            }

            return $lab_list;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return Lab::where('slug', $slug)->first();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTrendingLab($request){
        try {
            $lab_list = Lab::select()->where('labs.status', '1')->limit(20);
            if(isset($request->search)){
                $lab_list = $lab_list->where('labs.title','like', '%'.$request->search.'%');
            }
            return $lab_list->paginate(config('site-settings.pagination_per_page'));
        }catch(\Exception $e){
            return false;
        }
    }

    public function getLabsBasedOnSKillsAndTags($skills,$tags){
        try {
            /*gets Labs based on user skills*/
            $getLabsIdsBasedOnSKills=LabSkillsGroupsStackService::getLabIdBasesOnSKillsId($skills);
            /*gets Tags based on user tags*/
            $getLabsIdsBasedOnTags=LabTagsGroupsService::getLabsIdBasedOnTagsId($tags);
            $labIds= array_unique((array)array_merge($getLabsIdsBasedOnSKills, $getLabsIdsBasedOnTags));
            $labList = Lab::whereIn('labs.id',$labIds)->where('user_id','!=',auth()->user()->id)->take(12);
            return $labList->get();
        }catch(\Exception $e){
            return false;
        }
    }

    public function getTrendingTopics($request){
        try {
            $lab_list = Lab::select()->where('labs.status', '1')->limit(20);
            if(isset($request->search)){
                $lab_list = $lab_list->where('labs.title','like', '%'.$request->search.'%');
            }
            return $lab_list->paginate(config('site-settings.pagination_per_page'));
        }catch(\Exception $e){
            return false;
        }
    }
}
