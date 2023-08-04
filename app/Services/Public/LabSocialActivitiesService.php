<?php

namespace App\Services\Public;

use App\Models\LabSocialActivity;
use App\Models\OrganizationSocialActivities;
use Illuminate\Support\Facades\Auth;

class LabSocialActivitiesService
{
    public function checkSocialActivity($id,$column,$value)
    {
        try {
            return LabSocialActivity::where([
                'user_id' =>Auth::user()->id,
                'lab_id' =>$id,
                $column=>$value,
            ])->first();
        }catch (\Exception $e) {
            return false;
        }
    }

    public function update($id,$column,$action): bool
    {
        try {
            $records=LabSocialActivity::updateOrInsert(["user_id" => Auth::user()->id,
                "lab_id" => $id,
            ], [
                $column => $action,
            ]);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function getLabsBasedOnActivity($action){
        try{
            if(auth()->check()){
                $columnValue = self::getColumnNameValue($action);
                if($columnValue !== false){
                    $organization_ids = OrganizationSocialActivities::where(
                        [
                            'user_id' => auth()->user()->id,
                            $columnValue['column'] => $columnValue['action']
                        ])->get();
                    return $organization_ids;
                }
                return false;
            }
            return false;
        }catch(\Exception $e){
            return false;
        }
    }
}
