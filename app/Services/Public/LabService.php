<?php

namespace App\Services\Public;

use App\Models\Lab;
use App\Models\Organization;

class LabService
{
    public function getLabList($request){
        try {
            $lab_list = Lab::select()->where('labs.status', '1');
            $lab_list = self::filterLabList($request, $lab_list);
            return $lab_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public  function  filterLabList($request,$lab_list){
        try {
            if ($request->has('search') && !empty($request->search)){
                $lab_list = $lab_list->where('lab.title', 'like', '%'.$request->search.'%');
            }
            return $lab_list;
        }catch (\Exception $e){
            return false;
        }
    }
    public  function  getLabBasedOnSlug($slug){
        try {

            return Lab::where('slug', $slug)->first();
        } catch (\Exception $e) {
            return false;
        }
    }
}
