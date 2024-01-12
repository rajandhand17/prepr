<?php

namespace App\Services;

use App\Models\Friend;

class FriendService
{
    public function getColumnNameValue($action)
    {
        try {
            $value = null;
            switch($action) {
                case 'send':
                    $value = '0';
                    $column = 'status';
                    break;
                case 'follow':
                    $value = '0';
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

    public function checkAction($action)
    {
        try {
            $value = null;
            switch($action) {
                case 'accept':
                    $value = '1';
                    break;
                case 'reject':
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
            return false;
        }
    }

    public function createFriendsBasedOnAction($request, $column, $value)
    {
        try {
            $friendRequest = Friend::where(function ($query) use ($request) {
                $query->where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id])
                    ->orWhere(function ($query) use ($request) {
                        $query->where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id]);
                    });
            })->first();
            $secondColumn = $column == 'status' ? 'follow' : 'status';
            if (!$friendRequest) {
                $friendRequest = new Friend();
                $friendRequest->user_id = $request->user_id;
                $friendRequest->reference_id = auth()->user()->id;
                $friendRequest->$column = $value;
                $friendRequest->$secondColumn = '2';
                $friendRequest->save();
            } else {
                if ($friendRequest->$column == '2') {
                    $friendRequest->$column = '0';
                    $friendRequest->save();
                } else {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
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
            return false;
        }
    }

    public function getFriendsListing()
    {
        try {
            $friends = Friend::where(function ($query) {
                $query->where(['reference_id' => auth()->user()->id, 'status' => '1'])
                    ->orWhere(function ($query) {
                        $query->where(['user_id' => auth()->user()->id, 'status' => '1']);
                    });
            })->get();
            if ($friends) {
                return  $friends;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFollowersListing()
    {
        try {
            $followers = Friend::where(['user_id'=>auth()->user()->id, 'follow'=>'1'])->get();
            if ($followers) {
                return  $followers;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFollowListing()
    {
        try {
            $followers = Friend::where(['reference_id'=>auth()->user()->id, 'follow'=>'1'])->get();
            if ($followers) {
                return  $followers;
            }

            return false;
        } catch (\Exception $e) {
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
            return false;
        }
    }

    public function checkFollowStatus($request)
    {
        try {
            $follow = Friend::where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id, 'follow' => '1'])->first();

            return $follow;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkRequests($request)
    {
        try {
            $existsFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'status'=>'0'])->first();

            return $existsFriend;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkFollowRequests($request)
    {
        try {
            try {
                $existsFriend = Friend::where(['user_id' => auth()->user()->id, 'reference_id' => $request->user_id, 'follow'=>'0'])->first();

                return $existsFriend;
            } catch (\Exception $e) {
                return false;
            }
        } catch (\Exception $e) {
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
            return false;
        }
    }

    public function unfollowFriend($request)
    {
        try {
            $friend = Friend::where(['user_id' => $request->user_id, 'reference_id' => auth()->user()->id, 'follow'=>'1'])->first();
            if ($friend) {
                $friend->follow = '2';
                $friend->save();

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
