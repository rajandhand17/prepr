<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\Friend;

class FriendService
{
    public function checkAction($action)
    {
        try {
            $value = null;
            switch($action) {
                case 'send':
                    $value = '1';
                    break;
                case 'follow':
                    $value = '2';
                    break;
                case 'un-follow':
                    $value = '3';
                    break;
                case 'accept':
                    $value = '1';
                    break;
                case 'reject':
                    $value = '2';
                    break;
                case 'un-friend':
                    $value = '2';
                    break;
                default:
                    $value = null;
                    break;
            }
            if ($value !== null) {
                return $value;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getRecordsBasedOnId($request)
    {
        try {
            $friendRequest = Friend::where(function ($query) use ($request) {
                $query->where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id])
                    ->orWhere(function ($query) use ($request) {
                        $query->where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id]);
                    });
            })->first();

            return $friendRequest;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateFriendsBasedOnAction($request, $column, $value)
    {
        try {
            $friendRequest = Friend::where(function ($query) use ($request) {
                $query->where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id])
                    ->orWhere(function ($query) use ($request) {
                        $query->where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id]);
                    });
            })->first();
            if (!$friendRequest) {
                $friendRequest = new Friend();
                $friendRequest->user_id = $request->user_id;
                $friendRequest->reference_id = auth()->user()->id;
                $friendRequest->$column = $value;
                $friendRequest->save();
            } else {
                $friendRequest->$column = $value;
                $friendRequest->save();
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function friendRequestResponse($request, $value)
    {
        try {
            $friends = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id])->first();
            if ($friends) {
                $friends->status = $value;
                $friends->save();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function followRequestResponse($request, $value)
    {
        try {
            $friends = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id])->first();
            if ($friends) {
                $friends->follow = $value;
                $friends->save();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getFriendsListing($user = null)
    {
        try {
            $getUser = $user ?? auth()->user();

            $friends = Friend::where(function ($query) use ($getUser) {
                $query->where(['reference_id' => $getUser->id, 'status' => '1'])
                    ->orWhere(function ($query) use($getUser){
                        $query->where(['user_id' => $getUser->id, 'status' => '1']);
                    });
            })->get();
            if ($friends) {
                return  $friends;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getFollowersListing()
    {
        try {
            $followers = Friend::where(function ($query) {
                $query->where(['reference_id' => auth()->user()->id, 'user_follow' => '2'])
                    ->orWhere(function ($query) {
                        $query->where(['user_id' => auth()->user()->id, 'reference_follow' => '2']);
                    });
            })->get();
            if ($followers) {
                return  $followers;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getFollowListing()
    {
        try {
            $followers = Friend::where(function ($query) {
                $query->where(['reference_id' => auth()->user()->id, 'reference_follow' => '2'])
                    ->orWhere(function ($query) {
                        $query->where(['user_id' => auth()->user()->id, 'user_follow' => '2']);
                    });
            })->get();
            if ($followers) {
                return  $followers;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getFriendRequestList()
    {
        try {
            $friends = Friend::where(['user_id'=>auth()->user()->id, 'status'=>'0'])->get();
            if ($friends->count() > 0) {
                return  $friends;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getFollowersRequestList()
    {
        try {
            $follow = Friend::where(['user_id'=>auth()->user()->id, 'follow'=>'0'])->get();
            if ($follow->count() > 0) {
                return  $follow;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkFriendsStatus($request)
    {
        try {
            $existsFriend = Friend::where(function ($query) use ($request) {
                $query->where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id, 'status' => '1'])
                    ->orWhere(function ($query) use ($request) {
                        $query->where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'status' => '1']);
                    });
            })->first();

            return $existsFriend;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkRequests($request)
    {
        try {
            $existsFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'status'=>'0'])->first();

            return $existsFriend;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkFollowRequests($request)
    {
        try {
            try {
                $existsFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'reference_follow'=>'1'])->first();

                return $existsFriend;
            } catch (\Exception $e) {
                UtilityHelper::logError($e);

                return false;
            }
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function removeFriend($request)
    {
        try {
            $removedFriend = Friend::where(function ($query) use ($request) {
                $query->where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id, 'status' => '1'])
                    ->orWhere(function ($query) use ($request) {
                        $query->where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'status' => '1']);
                    });
            })->delete();
            if ($removedFriend) {
                return $removedFriend;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function unfollowFriend($request, $column)
    {
        try {
            $friend = Friend::where(function ($query) use ($request, $column) {
                $query->where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id, $column => '2'])
                    ->orWhere(function ($query) use ($request, $column) {
                        $query->where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, $column => '2']);
                    });
            })->first();
            if ($friend) {
                $friend->$column = '3';
                $friend->save();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function dashboardFriendList($userData)
    {
        try {
            $dashboardFriendList = Friend::where(['user_id' => $userData->id, 'status' => '0'])->take(5)->get();

            return $dashboardFriendList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
