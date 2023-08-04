<?php

namespace App\Services\Public;

use App\Models\Organization;
use App\Models\OrganizationSocialActivities;

class OrganizationService
{
    public static function getList($request)
    {
        try {
            $organization_list = Organization::select()->where('organizations.status', '1');

            $organization_list = self::filterOrganizationList($request, $organization_list);

            return $organization_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function filterOrganizationList($request, $organization_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $organization_list = $organization_list->where('organizations.title', 'like', '%'.$request->search.'%');
            }
            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $organization_list = $organization_list->whereIn('organizations.category', $request->category);
            }

            if($request->has('social_type') && !empty($request->social_type) && $request->social_type== 'liked'){
                $getOrganizationLikedList = OrganizationSocialActivitiesService::getOrganizationsBasedOnActivity('like');
                if($getOrganizationLikedList && $getOrganizationLikedList->count() > 0){
                    $organization_list = $organization_list->whereIn('organizations.id', $getOrganizationLikedList->pluck('organization_id'));
                }
            }

            if($request->has('social_type') && !empty($request->social_type) && $request->social_type== 'followed'){
                $getOrganizationLikedList = OrganizationSocialActivitiesService::getOrganizationsBasedOnActivity('follow');
                if($getOrganizationLikedList && $getOrganizationLikedList->count() > 0){
                    $organization_list = $organization_list->whereIn('organizations.id', $getOrganizationLikedList->pluck('organization_id'));
                }
            }

            if($request->has('social_type') && !empty($request->social_type) && $request->social_type== 'favourites'){
                $getOrganizationLikedList = OrganizationSocialActivitiesService::getOrganizationsBasedOnActivity('favourite');
                if($getOrganizationLikedList && $getOrganizationLikedList->count() > 0){
                    $organization_list = $organization_list->whereIn('organizations.id', $getOrganizationLikedList->pluck('organization_id'));
                }
            }


            return $organization_list;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getOrganizationBasedOnSlug($slug)
    {
        try {
            return Organization::where('slug', $slug)->first();
        } catch (\Exception $e) {
            return false;
        }
    }
}
