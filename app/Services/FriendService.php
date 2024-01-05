<?php

namespace App\Services;

use App\Models\Friend;

class FriendService
{
    public function sendFriendRequest($request)
    {
        try {
            $friend = Friend::updateOrCreate([
                'user_id'      => $request->user_id,
                'reference_id' => auth()->user()->id,
            ], [
                'user_id'      => $request->user_id,
                'reference_id' => auth()->user()->id,
                'status'       => '0',
            ]);

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getActionValue($action)
    {
        try {
            $value = null;
            switch($action) {
                case 'accept':
                    $value = '1';
                    $column='status';
                    break;
                case 'reject':
                    $value = '2';
                    $column='status';
                    break;
                case 'follow':
                    $value = '1';
                    $column='follow';
                    break;
                case 'un-follow':
                    $value = '2';
                    $column='follow';
                    break;
                default:
                    $value = null;
                    break;
            }
            if ($value !== null) {
                return ['column' => $column, 'value' => $value];
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendRequest($request)
    {
        try {
            $friendRequest = Friend::where([
                'user_id'=>$request->user_id,
                'reference_id'=>auth()->user()->id,
            ])->first();

            return $friendRequest;
        }catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendRequestBasedOnAction($request,$column,$value){
        try {
            $friendRequest = Friend::where([
                'user_id'=>auth()->user()->id,
                'reference_id'=>$request->user_id,
                $column=>$value,
            ])->first();
            return $friendRequest;
        }catch (\Exception $e) {
            return false;
        }
    }
    public function friendRequest($request, $column,$value)
    {
        try {
            $friends = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id])->first();
             if ($friends) {
                $friends->$column = $value;
                $friends->save();
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFriendsListing($columnName){
        try {
            $friends=Friend::where(['user_id'=>auth()->user()->id,$columnName=>'1'])->get();
            if($friends){
                return  $friends;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }

    public function getFriendRequestList($column){
        try {
            $friends=Friend::where(['user_id'=>auth()->user()->id,$column=>'0'])->get();
            if($friends->count()>0){
                return  $friends;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }

    public function getColumnName($column){
        try {
        $columnName=null;
        switch($column){
            case 'follow':
                $columnName='follow';
                break;
            case 'friends':
                $columnName='status';
                break;
            default:
                $columnName=null;
                break;
        }
        return $columnName;
        }catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendsStatus($request){
        try {
            $existsFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'status' => '1'])->first();
            return $existsFriend;
        }catch (\Exception $e) {
            return false;
        }
    }
    public function removeFriend($request){
        try {
            $removedFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'status' => '1'])->first();
            if ($removedFriend) {
                $removedFriend->status = '2';
                $removedFriend->follow = '2';
                $removedFriend->newsfeed = '2';
                $removedFriend->save();
            };
            return $removedFriend;
        }catch (\Exception $e) {
            return false;
        }
    }
}
