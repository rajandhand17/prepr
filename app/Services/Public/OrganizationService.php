<?php

namespace App\Services\Public;

use App\Models\Organization;

class OrganizationService
{
    public static function getOrganizationList($request)
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
            if ($request->has('search') && !empty($request->search)){
                $organization_list = $organization_list->where('organizations.title', 'like', '%'.$request->search.'%');
            }
            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $organization_list = $organization_list->whereIn('labs.category', $request->category);
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
