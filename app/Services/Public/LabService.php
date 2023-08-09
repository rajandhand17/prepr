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
            if ($request->has('privacy') && !empty($request->privacy)) {
                if ($request->privacy == 'Public' || $request->privacy == 'public') {
                    $lab_list = $lab_list->where('privacy', '=', 0);
                }
                if ($request->privacy == 'Private' || $request->privacy == 'private') {
                    $lab_list = $lab_list->where('privacy', '=', 1);
                }
            }
            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $lab_list = $lab_list->whereIn('labs.category', $request->category);
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $lab_list = $lab_list->where('organization_id', '=', $request->organization_id);
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
