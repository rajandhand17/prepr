<?php

namespace App\Services\Public;

use App\Models\OrganizationSocialActivities;
use Illuminate\Support\Facades\Auth;

class OrganizationSocialActivitiesService
{
    public function checkSocialActivity($id, $column, $action)
    {
        try {
            return OrganizationSocialActivities::where([
                ['organization_id', '=', $id],
                ['user_id', '=', Auth::user()->id],
                [$column, '=', $action],
            ])->first();
        } catch(\Exception $e) {
            return false;
        }
    }
    public function update($id,$column,$action){
        try{
            $records=OrganizationSocialActivities::updateOrInsert(["user_id" => Auth::user()->id,
                "organization_id" => $id,
            ],[
                $column => $action,
            ]);
            return true;
        }catch(\Exception $e){
            return false;
        }
     }
}
