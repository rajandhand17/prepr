<?php

namespace App\Services\Public;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Services\Manage\ChargebeeSubscriptionService;
use Exception;

class OrganizationService
{
    public static function getList($request)
    {
        try {
            $organization_list = Organization::select()->where('organizations.status', '1');
            $organization_list = self::filterOrganizationList($request, $organization_list);

            return $organization_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

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

            if ($request->has('is_verified') && !empty($request->is_verified)) {
                $is_verified = ($request->is_verified == 'yes') ? '1' : '0';
                $organization_list = $organization_list->where('organizations.is_verified', $is_verified);
            }

            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'liked') {
                $getOrganizationLikedList = OrganizationSocialActivitiesService::getOrganizationsBasedOnActivity('like');
                if ($getOrganizationLikedList && $getOrganizationLikedList->count() > 0) {
                    $organization_list = $organization_list->whereIn('id', $getOrganizationLikedList->pluck('organization_id'));
                }
            }

            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'followed') {
                $getOrganizationLikedList = OrganizationSocialActivitiesService::getOrganizationsBasedOnActivity('follow');
                if ($getOrganizationLikedList && $getOrganizationLikedList->count() > 0) {
                    $organization_list = $organization_list->whereIn('organizations.id', $getOrganizationLikedList->pluck('organization_id'));
                }
            }

            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'favourites') {
                $getOrganizationLikedList = OrganizationSocialActivitiesService::getOrganizationsBasedOnActivity('favourite');
                if ($getOrganizationLikedList && $getOrganizationLikedList->count() > 0) {
                    $organization_list = $organization_list->whereIn('organizations.id', $getOrganizationLikedList->pluck('organization_id'));
                }
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $organization_list = $organization_list->orderBy('organizations.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $organization_list = $organization_list->orderBy('organizations.title', 'DESC');
                        break;
                    case 'creation_date':
                        $organization_list = $organization_list->orderBy('organizations.created_at', 'ASC');
                        break;
                    default:
                        $organization_list = $organization_list->orderBy('organizations.id', 'ASC');
                }
            }

            if ($request->has('plan') && !empty($request->plan)) {
                $getPlan = config('chargebee.chargebee_plan.'.$request->plan);
                if ($getPlan) {
                    $getOrganizationIds = ChargebeeSubscriptionService::getChargebeeBasedOnSubscription($request->plan);
                    $organization_list = $organization_list->whereIn('organizations.id', $getOrganizationIds);
                }
            }

            if ($request->has('total_employees') && !empty($request->total_employees)) {
                switch ($request->total_employees) {
                    case '1':
                        $organization_list = $organization_list->whereBetween('total_employees', [0, 50]);
                        break;
                    case '2':
                        $organization_list = $organization_list->whereBetween('total_employees', [51, 250]);
                        break;
                    case '3':
                        $organization_list = $organization_list->whereBetween('total_employees', [251, 1000]);
                        break;
                    case '4':
                        $organization_list = $organization_list->where('total_employees', '>=', 1000);
                        break;
                }
            }

            if ($request->has('request') && $request->get('request')) {
                $getOrganizationIds = MemberManagementService::getOrganizationIds(auth()->user()->email, $request->get('request'));
                if ($getOrganizationIds) {
                    $organization_list = $organization_list->whereIn('id', $getOrganizationIds);
                }
            }

            return $organization_list;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationBasedOnSlug($slug)
    {
        try {
            return Organization::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationExistBasedOnUuidArray($uuid)
    {
        try {
            $organization = Organization::select('id')->whereIn('uuid', $uuid)->get();
            if ($organization != null) {
                return $organization;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchOrganizationIds($userId)
    {
        try {
            $organizations = Organization::where('user_id', $userId)->pluck('id');

            return $organizations;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function fetchOrganizations($request, $organizationIds)
    {
        try {
            $limit = config('site-settings.listing_limit');
            $organization_list = Organization::select('id', 'uuid', 'title', 'slug', 'cover_image', 'profile_image')->whereIn('id', $organizationIds);
            $fetchOrganizations = self::filterOrganizationList($request, $organization_list);
            if ($fetchOrganizations->get()->isEmpty()) {
                //Statically sending back if no org is available then sending back Prepr organization
                $fetchOrganizations = Organization::select('id', 'uuid', 'title', 'slug', 'cover_image', 'profile_image')->where('id', '19');
            }

            return $fetchOrganizations->paginate(config('site-settings.switcher_listing_limit'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
