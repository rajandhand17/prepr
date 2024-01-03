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

        public function acceptFriendRequest($request){
            try {
                $acceptFriendRequest=Friend::where(['user_id'=>auth()->user()->id,'reference_id'=>$request->reference_id,'status'=>'0'])->first();
                $acceptFriendRequest->status=1;
                $acceptFriendRequest->save();
                return $acceptFriendRequest;
            }catch (\Exception $e) {
                return false;
            }
        }

        public function rejectFriendRequest($request){
            try {
                $rejectFriendRequest=Friend::where(['user_id'=>auth()->user()->id,'reference_id'=>$request->reference_id,'status'=>'0'])->first();
                $rejectFriendRequest->status='2';
                $rejectFriendRequest->save();
                return true;
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
