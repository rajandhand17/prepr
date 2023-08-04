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
            if($request->has('social_type') && !empty($request->social_type) && $request->social_type== 'join'){
                $getLabJoinedList = LabSocialActivitiesService::getLabBasedOnActivity('join');
                if($getLabJoinedList && $getLabJoinedList->count() > 0){
                    $lab_list = $lab_list->whereIn('id', $getLabJoinedList->pluck('lab_id'));
                }
            }
            if($request->has('social_type') && !empty($request->social_type) && $request->social_type== 'followed'){
                $getLabJoinedList = LabSocialActivitiesService::getLabBasedOnActivity('follow');
                if($getLabJoinedList && $getLabJoinedList->count() > 0){
                    $lab_list = $lab_list->whereIn('id', $getLabJoinedList->pluck('lab_id'));
                }
            }
            if($request->has('social_type') && !empty($request->social_type) && $request->social_type== 'favourite'){
                $getLabJoinedList = LabSocialActivitiesService::getLabBasedOnActivity('favourite');
                if($getLabJoinedList && $getLabJoinedList->count() > 0){
                    $lab_list = $lab_list->whereIn('id', $getLabJoinedList->pluck('lab_id'));
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
