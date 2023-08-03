<?php

namespace App\Services\Public;

use App\Models\OrganizationSocialActivities;
use Illuminate\Support\Facades\Auth;

class OrganizationSocialActivitiesService
{
    public function checkExists($id,$column,$action){
        try{
            return OrganizationSocialActivities::where([
                ['organization_id', '=', $id],
                ['user_id', '=', Auth::user()->id],
                [$column, '=', $action],
            ])->first();
        }catch(\Exception $e){
            return false;
        }
    }

    public function organizationSocialActivitiesService($id,$column,$action){
        try{
            $uniqueKey = ["user_id" => Auth::user()->id,
                "organization_id" => $id,
            ];
            $productData = [
                $column => $action,
            ];
            $records=OrganizationSocialActivities::updateOrInsert($uniqueKey, $productData);
            if($records){
                return true;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
}
