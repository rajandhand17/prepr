<?php

namespace App\Services\Public;

use App\Models\Lab;

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
                $lab_list = $lab_list->where('lab.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $lab_list = $lab_list->whereIn('labs.category', $request->category);
            }

            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $lab_list = $lab_list->where('organization_id', '=', $request->organization_id);
            }
            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'liked') {
                $getLabLikedList = LabSocialActivitiesService::getLabsBasedOnActivity('like');
                if ($getLabLikedList && $getLabLikedList->count() > 0) {
                    $lab_list = $lab_list->whereIn('id', $getLabLikedList->pluck('lab_id'));
                }
            }
            if ($request->has('social_type') && !empty($request->social_type) && $request->social_type == 'favourites') {
                $getLabFavouriteList = LabSocialActivitiesService::getLabsBasedOnActivity('favourite');
                if ($getLabFavouriteList && $getLabFavouriteList->count() > 0) {
                    $lab_list = $lab_list->whereIn('id', $getLabFavouriteList->pluck('lab_id'));
                }
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $lab_list = $lab_list->orderBy('labs.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $lab_list = $lab_list->orderBy('labs.title', 'DESC');
                        break;
                    case 'creation_date':
                        $lab_list = $lab_list->orderBy('labs.created_at', 'ASC');
                        break;
                    default:
                        $lab_list = $lab_list->orderBy('labs.id', 'ASC');
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
}
