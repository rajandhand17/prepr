<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;

class UserService
{
    public static function joinName($firstName, $lastName)
    {
        return $firstName.' '.$lastName;
    }

    public static function getUserByEmail($email)
    {
        try {
            $user = User::select([
                'id', 'preferred_language', 'first_name', 'last_name', 'full_name', 'username', 'email', 'country_code', 'phone_number',
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'is_profile_completed', 'created_at',
            ])->where('email', $email)->first();
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUserById($id)
    {
        try {
            $user = User::find($id);
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUserByGO1Id($go1UserId)
    {
        try {
            return User::where('go1_id', $go1UserId)->first();
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function getUserByUsername($username)
    {
        try {
            $user = User::select([
                'id', 'preferred_language', 'first_name', 'last_name', 'full_name', 'username', 'email', 'country_code', 'phone_number',
                'profile_image', 'user_points', 'user_rank', 'verified_user', 'is_profile_completed', 'created_at',
            ])->where('username', $username)->first();
            if ($user != null) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUsers($request)
    {
        try {
            $user = User::select();
            if ($request->search) {
                $user = $user->orWhere('full_name', 'like', '%'.$request->search.'%')->orWhere('username', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%');
            }
            $user = $user->take(config('site-settings.pagination_per_page'))->get();

            return $user;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function addUserName($request)
    {
        try {
            $updateUser = User::where('id', auth()->user()->id)->first();
            $updateUser->full_name = $request->name;
            $updateUser->save();

            return $updateUser;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updateUserAccount($request)
    {
        try {
            $user = auth()->user();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $request->first_name.' '.$request->last_name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->preferred_language = $request->preferred_language;
            $user->preferred_timezone = $request->preferred_timezone;
            $user->two_factor_verification = ($request->two_factor_verification == 'yes') ? '1' : '0';
            $user->save();

            return $user;
        } catch (Exception $e) {
            return false;
        }
    }

    public function changePassword($request)
    {
        try {
            $user = auth()->user();
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function removeProfileImage()
    {
        try {
            $user = auth()->user();
            if ($user) {
                $user->profile_image = config('site-settings.default_user_profile_image');
                $user->save();

                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deactivateUserAccount()
    {
        try {
            $user = auth()->user();
            $user->is_deactivated = '1';
            $user->save();
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUserIdsByEmail($emailIds)
    {
        try {
            $fetchusers = User::whereIn('email', $emailIds)->pluck('id');

            return $fetchusers;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUsersByIds($ids)
    {
        try {
            $fetchUsers = User::whereIn('id', $ids)->get();

            return  $fetchUsers;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function mapGO1User($go1UserId, $response)
    {
        try {
            return User::query()->where('id', auth()->user()->id)->update(['go1_id' => $go1UserId, 'go1_user_metadata' => $response]);
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function getLeaderBoardList($request){
        try {
            $authUserId = auth()->user()->id;
            $users =User::select();
            $users=self::filterLeaderboardUsers($users,$request);
            $users=$users->pluck('id');
            if ($users->contains($authUserId)) {
                $users = $users->reject(function ($user) use ($authUserId) {
                    return $user=== $authUserId;
                });
            }
            $userIds=$users->prepend($authUserId)->all();
            $userRecords = User::whereIn('id', $userIds)
                ->orderByRaw("FIELD(id, " . implode(',', $userIds) . ")")
                ->get();
            return $userRecords;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function filterLeaderboardUsers($users,$request){
        try {
            if ($request->has('lab_id') && !empty($request->lab_id)) {
                $users = $users->whereHas('userLabs', function ($query) use ($request){
                    $query->whereIn('id', $request->lab_id);
                });
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $users = $users->whereHas('userOrganization', function ($query) use ($request){
                    $query->whereIn('id', $request->organization_id);
                });
            }
            if ($request->has('challenge_id') && !empty($request->challenge_id)) {
                $users = $users->whereHas('userChallenge', function ($query) use ($request){
                    $query->whereIn('id', $request->challenge_id);
                });
            }
            if ($request->has('project_id') && !empty($request->project_id)) {
                $users = $users->whereHas('userProjects', function ($query) use ($request){
                    $query->whereIn('id', $request->project_id);
                });
            }
            switch ($request->sort_by) {
                    case 'learning_points':
                        $users=$users->withCount(['userPoints as user_points_count' => function ($query) {
                            $query->whereNull('deleted_at');
                        }])->orderByDesc('user_points_count')->take(20);
                        break;
                    case 'learning_rank':
                        $users=$users->withCount(['userRank as user_rank_count' => function ($query) {
                            $query->whereNull('deleted_at');
                        }])->orderByDesc('user_rank_count')->take(20);
                        break;
                    case 'achievement':
                        $users=$users->withCount(['userAchievements as user_achievement_count' => function ($query) {
                            $query->whereNull('deleted_at');
                        }])->orderByDesc('user_achievement_count')->take(20);
                        break;
                    default:
                        $users=$users->withCount(['userPoints as user_points_count' => function ($query) {
                            $query->whereNull('deleted_at');
                        }])
                            ->orderByDesc('user_points_count')
                            ->take(20);
                }
            return $users;
        }catch (\Exception $e){
            return false;
        }
    }
}
