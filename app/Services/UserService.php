<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\User;
use App\Models\UserPersonal;
use Exception;
use Illuminate\Support\Facades\Hash;

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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserByGO1Id($go1UserId)
    {
        try {
            return User::where('go1_id', $go1UserId)->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

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
            $user->phone_number = $request->phone_number;
            $user->preferred_language = $request->preferred_language;
            $user->preferred_timezone = $request->preferred_timezone;
            $user->two_factor_verification = ($request->two_factor_verification == 'yes') ? '1' : '0';
            $user->save();

            return $user;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

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
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserIdsByEmail($emailIds)
    {
        try {
            $fetchusers = User::whereIn('email', $emailIds)->pluck('id');

            return $fetchusers;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUsersByIds($ids)
    {
        try {
            $fetchUsers = User::whereIn('id', $ids)->get();

            return  $fetchUsers;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function mapGO1User($go1UserId, $response)
    {
        try {
            return User::query()->where('id', auth()->user()->id)->update(['go1_id' => $go1UserId, 'go1_user_metadata' => $response]);
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getLeaderBoardList($request, $emails)
    {
        try {
            $authUserId = auth()->user()->id;
            $users = User::select()->orderBy('user_rank');
            $users = self::filterLeaderboardUsers($users, $request, $emails);
            $users = $users->pluck('id');
            if ($users->contains($authUserId)) {
                $users = $users->reject(function ($user) use ($authUserId) {
                    return $user === $authUserId;
                });
            }
            $userIds = $users->prepend($authUserId)->all();
            $userRecords = User::whereIn('id', $userIds)
                ->orderByRaw('FIELD(id, '.implode(',', $userIds).')')
                ->paginate(config('site-settings.pagination_per_page'));

            return $userRecords;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getComponentBasedUsers($membersEmails, $request)
    {
        try {
            $users = User::whereIn('email', $membersEmails);
            $users = self::filterLeaderboardUsers($users, $request);
            $userIds = $users->pluck('id')->all();
            $userRecords = User::whereIn('id', $userIds)
                    ->orderByRaw('FIELD(id, '.implode(',', $userIds).')')
                   ->paginate(config('site-settings.pagination_per_page'));

            return $userRecords;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterLeaderboardUsers($users, $request, $membersEmails = null)
    {
        try {
            if (isset($membersEmails) && count($membersEmails) > 0 && $membersEmails !== null) {
                $users = $users->whereIn('email', $membersEmails);
            }
            switch ($request->sort_by) {
                case 'learning_points':
                    $users = $users->orderBy('user_points', 'desc')->take(20);
                    break;
                case 'learning_rank':
                    $users = $users->orderBy('user_rank', 'desc')->take(20);
                    break;
                case 'achievement':
                    $users = $users->orderBy('achievement_count', 'desc')->take(20);
                    break;
                default:
                    $users = $users->orderBy('user_points', 'desc')->take(20);
            }

            return $users;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function setOrganizationPreference($organizationId)
    {
        try {
            $user = auth()->user();
            $user->preferred_organization = $organizationId;
            $user->save();

            return $user;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return null;
        }
    }

    public static function getUserByEmailArray($emailArrayIds)
    {
        try {
            $getUserByEmailArray = User::whereIn('email', $emailArrayIds)->get()->pluck('email');

            return $getUserByEmailArray;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function userOnboarding()
    {
        try {
            $user = auth()->user();
            $user->update(['is_onboarding_completed' => '1']);

            return $user;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function completeLabMiniOnBoarding()
    {
        try {
            $user = auth()->user();
            $user->update(['display_lab_mini_onboarding' => '1']);

            return $user;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function completeChallengeMiniOnBoarding()
    {
        try {
            $user = auth()->user();
            $user->update(['display_challenge_mini_onboarding' => '1']);

            return $user;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function completeOrganizationMiniOnBoarding()
    {
        try {
            $user = auth()->user();
            $user->update(['display_organization_mini_onboarding' => '1']);

            return $user;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserByEmails($emails)
    {
        try {
            return User::whereIn('email', $emails)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getUserBasedOnMagnetUserId($magnetUserId)
    {
        try {
            return User::where('magnet_user_id', $magnetUserId)->first();
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function updateUserMagnetId($userId, $magnetId)
    {
        try {
            $user = User::find($userId);
            $user->magnet_user_id = $magnetId;
            $user->save();

            return $user;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function registerMagnetUser($data)
    {
        try {
            $user = User::query()->create([
                'first_name'     => $data->first_name,
                'last_name'      => $data->last_name,
                'username'       => $data->user_name,
                'email'          => $data->email,
                'phone_number'   => $data->telephone,
                'full_name'      => $data->first_name.' '.$data->last_name,
                'magnet_user_id' => $data->id,
            ]);
            UserPersonal::query()->create([
                'user_id'   => $user->id,
                'user_type' => $data->type,
                'status'    => $data->status,
            ]);

            return $user;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function registerOrUpdateMagnetUsers($users)
    {
        try {
            $registeredOrUpdatedUsers = [];
            foreach ($users as $item) {
                $userByMagnetId = self::getUserBasedOnMagnetUserId($item['id']);
                if ($userByMagnetId) {
                    $registeredOrUpdatedUsers[] = $userByMagnetId;
                    continue;
                }

                $userByEmail = self::getUserByEmail($item['email']);
                if (!$userByEmail) {
                    $newUser = UserService::registerMagnetUser((object) $item);
                    if (!$newUser) {
                        return false;
                    }
                    $registeredOrUpdatedUsers[] = $newUser;
                } else {
                    $updatedUser = UserService::updateUserMagnetId($userByEmail->id, $item['id']);
                    if (!$updatedUser) {
                        return false;
                    }
                    $registeredOrUpdatedUsers[] = $userByEmail;
                }
            }

            return $registeredOrUpdatedUsers;
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function updateUserPoint($userIdsArray, $userPoint)
    {
        try {
            if (empty($userIdsArray)) {
                return false;
            }

            foreach ($userIdsArray as $userId) {
                $fetchUserById = $this->getUserById($userId);

                if (!$fetchUserById) {
                    continue;
                }

                $fetchUserById->user_points += $userPoint;
                $fetchUserById->save();
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function organizationPreferenceUpdate($organizationId)
    {
        try {
            $updateListPreference = User::where('preferred_organization', $organizationId)->update(['preferred_organization' => null]);

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateFcmToken($request)
    {
        try {
            auth()->user()->update([
                'fcm_token' => $request->fcm_token,
            ]);

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkComponentMiniOnBoard($component)
    {
        try {
            $userData = auth()->user();
            $response = false;
            switch ($component) {
                case 'lab':
                    if ($userData->display_lab_mini_onboarding == '1') {
                        $response = true;
                    }
                    break;
                case 'challenge':
                    if ($userData->display_challenge_mini_onboarding == '1') {
                        $response = true;
                    }
                    break;
                case 'organization':
                    if ($userData->display_organization_mini_onboarding == '1') {
                        $response = true;
                    }
                    break;
            }

            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }
}
