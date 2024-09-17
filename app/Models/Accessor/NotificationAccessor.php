<?php

namespace App\Models\Accessor;

use App\Models\Challenge;
use App\Models\ChallengePath;
use App\Models\Friend;
use App\Models\Lab;
use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use App\Notifications\NotificationTypes;

trait NotificationAccessor
{
    public function getFriendRequestFromAttribute(): ?array
    {
        if ($this->type === NotificationTypes::FRIEND_REQUEST) {
            $requestFromId = data_get($this->data, 'friend_request_from');
            $friendRequestId = data_get($this->data, 'friend_request_id');
            if ($requestFromId && $friendRequestId) {
                $user = User::query()->where('id', '=', $requestFromId)->first();
                $friend = Friend::query()->where('id', '=', $friendRequestId)->first();

                $requestStatusMap = [
                    '0' => 'pending',
                    '1' => 'accepted',
                    '2' => 'rejected',
                ];

                return [
                    'friend_request_status' => data_get($requestStatusMap, data_get($friend, 'status')),
                    'request_from'          => ['id' => data_get($user, 'id'),
                        'full_name'                  => data_get($user, 'full_name'),
                        'first_name'                 => data_get($user, 'first_name'),
                        'last_name'                  => data_get($user, 'last_name'),
                        'username'                   => data_get($user, 'username'),
                        'email'                      => data_get($user, 'email'),
                        'profile_image'              => data_get($user, 'profile_image'), ],
                ];
            }
        }

        return null;
    }

    public function getFormattedChallengeAttribute(): ?array
    {
        if ($this->type === NotificationTypes::CHALLENGE) {
            $challengeId = data_get($this->data, 'module_id');
            $requestFromId = data_get($this->data, 'invitation_from_id');
            if ($challengeId) {
                $challenge = Challenge::query()->where('id', '=', $challengeId)->first();
                $user = User::query()->where('id', '=', $requestFromId)->first();

                if ($challenge && $user) {
                    return [
                        'challenge' => ['id' => data_get($challenge, 'id'),
                            'slug'           => data_get($challenge, 'slug'),
                            'title'          => data_get($challenge, 'title')],
                        'invitation_from' => ['id' => data_get($user, 'id'),
                            'full_name'            => data_get($user, 'full_name'),
                            'first_name'           => data_get($user, 'first_name'),
                            'last_name'            => data_get($user, 'last_name'),
                            'username'             => data_get($user, 'username'),
                            'email'                => data_get($user, 'email'),
                            'profile_image'        => data_get($user, 'profile_image'), ],
                    ];
                }
            }
        }

        return null;
    }

    public function getFormattedOrganizationAttribute(): ?array
    {
        if ($this->type === NotificationTypes::ORGANIZATION) {
            $organizationId = data_get($this->data, 'module_id');
            $requestFromId = data_get($this->data, 'invitation_from_id');

            if ($organizationId && $requestFromId) {
                $organization = Organization::query()->where('id', '=', $organizationId)->first();
                $user = User::query()->where('id', '=', $requestFromId)->first();

                if ($organization && $user) {
                    return [
                        'organization' => ['id' => data_get($organization, 'id'),
                            'slug'              => data_get($organization, 'slug'),
                            'title'             => data_get($organization, 'title')],
                        'invitation_from' => ['id' => data_get($user, 'id'),
                            'full_name'            => data_get($user, 'full_name'),
                            'first_name'           => data_get($user, 'first_name'),
                            'last_name'            => data_get($user, 'last_name'),
                            'username'             => data_get($user, 'username'),
                            'email'                => data_get($user, 'email'),
                            'profile_image'        => data_get($user, 'profile_image'), ],
                    ];
                }
            }
        }

        return null;
    }

    public function getFormattedLabAttribute(): ?array
    {
        if ($this->type === NotificationTypes::LAB) {
            $labId = data_get($this->data, 'module_id');
            $requestFromId = data_get($this->data, 'invitation_from_id');

            if ($labId && $requestFromId) {
                $lab = Lab::query()->where('id', '=', $labId)->first();
                $user = User::query()->where('id', '=', $requestFromId)->first();

                if ($lab && $user) {
                    return [
                        'lab' => ['id' => data_get($lab, 'id'),
                            'slug'     => data_get($lab, 'slug'),
                            'title'    => data_get($lab, 'title')],
                        'invitation_from' => ['id' => data_get($user, 'id'),
                            'full_name'            => data_get($user, 'full_name'),
                            'first_name'           => data_get($user, 'first_name'),
                            'username'             => data_get($user, 'username'),
                            'last_name'            => data_get($user, 'last_name'),
                            'email'                => data_get($user, 'email'),
                            'profile_image'        => data_get($user, 'profile_image'), ],
                    ];
                }
            }
        }

        return null;
    }

    public function getFormattedModuleAttribute(): ?array
    {
        if ($this->type === NotificationTypes::COMMENT) {
            $moduleId = data_get($this->data, 'module_id');
            $moduleType = data_get($this->data, 'module_type');
            $userId = data_get($this->data, 'user_id');

            if ($moduleId && $moduleType) {
                $module = null;
                switch ($moduleType) {
                    case 'lab':
                        $module = Lab::query()->where('id', '=', $moduleId)->first();
                        break;

                    case 'project':
                        $module = Project::query()->where('id', '=', $moduleId)->first();
                        break;

                    case 'challenge':
                        $module = Challenge::query()->where('id', '=', $moduleId)->first();
                        break;

                    case 'challenge-path':
                        $module = ChallengePath::query()->where('id', '=', $moduleId)->first();
                        break;
                }

                $user = User::query()->where('id', '=', $userId)->first();

                if ($module && $user) {
                    return [
                        'module' => ['id' => data_get($module, 'id'),
                            'slug'     => data_get($module, 'slug'),
                            'type'     => $moduleType,
                            'title'    => data_get($module, 'title')],
                        'commented_by' => ['id' => data_get($user, 'id'),
                            'full_name'            => data_get($user, 'full_name'),
                            'first_name'           => data_get($user, 'first_name'),
                            'username'             => data_get($user, 'username'),
                            'last_name'            => data_get($user, 'last_name'),
                            'email'                => data_get($user, 'email'),
                            'profile_image'        => data_get($user, 'profile_image'), ],
                    ];
                }
            }
        }

        return null;
    }
}
