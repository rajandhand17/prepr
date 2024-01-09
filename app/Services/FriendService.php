<?php

namespace App\Services;

use App\Models\Friend;

class FriendService
{
    public function sendFriendRequest($request)
    {
        try {
            $friend=Friend::updateOrCreate([
                'reference_id'=>auth()->user()->id,
                'user_id'=>$request->user_id,
            ],[
                'reference_id'=>auth()->user()->id,
                'user_id'=>$request->user_id,
                'status'=>'0',
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
                case 'send':
                    $value='0';
                    $column='status';
                    break;
                case 'follow':
                    $value = '1';
                    $column = 'follow';
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
                'user_id'     => $request->user_id,
                'reference_id'=> auth()->user()->id,
            ])->first();
            return $friendRequest;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkAction($action){
        try {
            $value = null;
            switch($action) {
                case 'accept':
                    $value='1';
                    $column='status';
                    break;
                case 'follow':
                    $value = '1';
                    $column = 'follow';
                    break;
                default:
                    $value = null;
                    break;
            }
            if ($value !== null) {
                return ['column' => $column, 'value' => $value];
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }
    public function createFriendsBasedOnAction($request,$column,$value)
    {
        try {
            $friendRequest=new Friend();
            $friendRequest->user_id = $request->user_id;
            $friendRequest->reference_id = auth()->user()->id;
            $friendRequest->$column=$value;
            $friendRequest->save();
            return $friendRequest;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateFriendsBasedOnAction($request, $column, $value){
        try {
            $friends = Friend::where(['user_id' =>$request->user_id, 'reference_id' =>auth()->user()->id])->first();
            if ($friends) {
                $friends->$column = $value;
                $friends->save();
                return true;
            }
            return false;
        }catch (\Exception $e) {
            return false;
        }
    }
    public function responseOfFriendRequest($request, $column, $value)
    {
        try {
            $friends = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id])->first();
            if ($friends) {
                $friends->$column = $value;
                $friends->save();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFriendsListing($columnName)
    {
        try {
            $friends = Friend::where(['user_id'=>auth()->user()->id, $columnName=>'1'])->get();
            if ($friends) {
                return  $friends;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFriendRequestList($column)
    {
        try {
            $friends = Friend::where(['user_id'=>auth()->user()->id, $column=>'0'])->get();
            if ($friends->count() > 0) {
                return  $friends;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getColumnName($column)
    {
        try {
            $columnName = null;
            switch($column) {
                case 'follow-list':
                    $columnName = 'follow';
                    break;
                case 'list':
                    $columnName = 'status';
                    break;
                default:
                    $columnName = null;
                    break;
            }

            return $columnName;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFriendsStatus($request)
    {
        try {
            $existsFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'status' => '1'])->first();

            return $existsFriend;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function removeFriend($request)
    {
        try {
            $removedFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id])->delete();
            if ($removedFriend) {
                return $removedFriend;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
