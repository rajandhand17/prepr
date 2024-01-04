<?php

namespace App\Services;

use App\Models\CampusConnectStudentInformation;
use App\Models\Friend;
use App\Models\UserEducation;
use App\Models\UserPersonal;

class FriendService
{
        public function sendFriendRequest($request){
            try {
                $friend=Friend::updateOrCreate([
                    'user_id' => auth()->user()->id,
                    'reference_id' => $request->reference_id,
                ], [
                    'user_id'      =>  auth()->user()->id,
                    'reference_id' => $request->reference_id,
                    'status'       =>'0',
                ]);
                return $friend;
            }catch(\Exception $e) {
                return false;
            }
        }

        public function friendRequest($request,$action){
            try {
                $acceptFriendRequest=Friend::where(['user_id'=>auth()->user()->id,'reference_id'=>$request->reference_id,'status'=>'0'])->first();
                $acceptFriendRequest->status=$action;
                $acceptFriendRequest->save();
                return $acceptFriendRequest;
            }catch (\Exception $e) {
                return false;
            }
        }

        public function getActionValue($action){
            try {
                $value=null;
                switch($action){
                    case 'accept':
                        $value='1';
                        break;
                    case 'reject':
                        $value='2';
                        break;
                    default:
                        $value=null;
                        break;
                }
                if($value!==null){
                    return $value;
                }
                return false;
            }catch (\Exception $e) {
                return false;
            }
        }

        public function checkFriendRequest($request){
            try {
                $friendRequest=Friend::where(['user_id'=>auth()->user()->id,'reference_id'=>$request->reference_id])->first();
                return $friendRequest;
            }catch (\Exception $e) {
                return false;
            }
        }
}
