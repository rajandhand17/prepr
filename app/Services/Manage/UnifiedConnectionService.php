<?php

namespace App\Services\Manage;

use App\Helpers\CryptHelper;
use App\Helpers\Unified\UnifiedHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\Lab;
use App\Models\Organization;
use App\Models\Role;
use App\Models\UnifiedConnection;
use App\Models\User;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UnifiedConnectionService
{
    public function getIntegrations($data, User $user): array|false
    {
        try {
            $model = $this->getModelFromComponent(data_get($data, 'usage_type', ''));
            $integrations = UnifiedHelper::getIntegrations();
            if ($integrations === false || $model === false) {
                return false;
            }

            $formatted = collect($integrations->json())->map(function ($connection) use ($data, $model, $user) {
                // STATE FOR ACTIONS
                $state = [
                    'component_slug'  => data_get($data, 'component_slug'),
                    'usage_type'      => data_get($data, 'usage_type'),
                    'connection_type' => data_get($connection, 'type'),
                ];

                // STATE IS ENCRYPTED TO AVOID USER MANIPULATION
                $encryptedState = CryptHelper::encrypt($state);
                if (config('unified.use_faker')) { // RETURN FAKE CONNECTION
                    return [
                        ...$connection,
                        'existing_connection' => true,
                        'connection_id'       => Randomize::chars(30)->alphanumeric()->unique()->generate(),
                        'state'               => $encryptedState,
                    ];
                }

                // CHECKING IF EXISTING CONNECTION EXISTS FOR REUSE
                $existingConnection = $this->getExistingConnection($model, [
                    'component_slug'  => data_get($data, 'component_slug'),
                    'connection_type' => data_get($connection, 'type'),
                    'user_id'         => data_get($user, 'id'),
                    'usage_type'      => data_get($data, 'usage_type'),
                ]);

                if ($existingConnection) { // RETURNING CONNECTION ID
                    return [
                        ...$connection,
                        'existing_connection' => true,
                        'connection_id'       => $existingConnection->connection_id,
                        'state'               => $encryptedState,
                    ];
                }

                // AUTH URL PARAMETERS
                $parameters = [
                    'redirect' => 1,
                    'env'      => config('unified.env'),
                    'prompt'   => 'consent',
                    'state'    => $encryptedState,
                ];

                return [
                    ...$connection,
                    'existing_connection' => false,
                    'unified_login_url'   => sprintf(
                        config('unified.url_paths.unified_login_url'),
                        UtilityHelper::sanitizeUrl(config('unified.base_url')),
                        config('unified.workspace'),
                        data_get($connection, 'type'),
                        http_build_query($parameters)
                    ),
                ];
            });

            return $formatted->toArray();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    /**
     * @param $string
     *
     * @return false|string
     */
    public function getModelFromComponent($string): false|string
    {
        return match ($string) {
            'organization_member_invite' => Organization::class,
            'lab_member_invite'          => Lab::class,
            'challenge_member_invite'    => Challenge::class,
            default                      => false
        };
    }

    public function getExistingConnection($model, array $data): UnifiedConnection|false
    {
        try {
            $entity = $this->getComponentBasedOnModel(data_get($data, 'component_slug'), $model);
            if ($entity === false) {
                return false;
            }
            /** @var UnifiedConnection $connection */
            $connection = UnifiedConnection::query()->where([
                'model_id'        => data_get($entity, 'id'),
                'model_type'      => $model,
                'connection_type' => data_get($data, 'connection_type'),
                'user_id'         => data_get($data, 'user_id'),
                'usage_type'      => config('unified.usage_types.'.data_get($data, 'usage_type', '')),
            ])->first();

            return $connection ?: false;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    public function getComponentBasedOnModel($componentSlug, $model)
    {
        $component = match ($model) {
            Challenge::class    => ChallengeService::getChallengeBasedOnSlug($componentSlug),
            Organization::class => OrganizationService::getOrganizationBasedOnSlug($componentSlug),
            Lab::class          => LabService::getLabBasedOnSlug($componentSlug),
            default             => false,
        };

        if (!$component) {
            return false;
        }

        return $component;
    }

    /**
     * @param $connectionId
     * @param $stateData
     *
     * @return Model|Builder|false
     */
    public function createConnection($connectionId, $stateData, $user = null): Model|Builder|false
    {
        try {
            $user = $user ?? auth()->user();
            if (!$stateData) {
                return false;
            }
            $model = $this->getModelFromComponent(data_get($stateData, 'usage_type', ''));
            if (!$model) {
                return false;
            }

            $componentSlug = data_get($stateData, 'component_slug');
            $component = $this->getComponentBasedOnModel($componentSlug, $model);

            return UnifiedConnection::query()->firstOrCreate(
                [
                    'model_type'      => $model,
                    'model_id'        => $component->id,
                    'connection_type' => data_get($stateData, 'connection_type'),
                    'user_id'         => $user->id,
                    'usage_type'      => config('unified.usage_types.'.data_get($stateData, 'usage_type', '')),
                ],
                ['connection_id' => $connectionId]
            );
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }

    /**
     * @param string $usageType
     *
     * @return false|string
     */
    public function getComponentNameFormUsage(string $usageType): false|string
    {
        return match ($usageType) {
            'organization_member_invite' => 'organization',
            'lab_member_invite'          => 'lab',
            'challenge_member_invite'    => 'challenge',
            default                      => false
        };
    }

    /**
     * @param array $data
     *
     * @return false|array
     */
    public function inviteMembers(array $data): false|array
    {
        try {
            $usageType = data_get($data, 'state.usage_type', '');
            $component = $this->getComponentBasedOnModel(
                data_get($data, 'state.component_slug', ''),
                $this->getModelFromComponent($usageType)
            );
            $componentName = $this->getComponentNameFormUsage($usageType);

            if ($component === false || $componentName === false) {
                return false;
            }

            $roles = Role::query()->get()->keyBy('display_name')->map(function ($data) {
                return $data->name;
            })->toArray();
            /**
             * FORMATTED MEMBER LIST.
             */
            $formattedMembersList = collect(data_get($data, 'members', []))->map(function ($member) use ($usageType, $roles) {
                return [
                    'type'          => config('constants.member_management_type.invite'),
                    'invite_type'   => config('constants.member_management_invite_type.unified'),
                    'invitee_name'  => data_get($member, 'name'),
                    'invitee_email' => data_get($member, 'email'),
                    'role'          => $usageType !== 'organization_member_invite' ? 'user' : data_get($roles, data_get($member, 'role', 'User')),
                ];
            });

            $inviteMember = MemberManagementService::addMembers(
                $component,
                $componentName,
                (object) [
                    'auto_invite'  => 'Yes',
                    'email_status' => 'scheduled',
                    'subject_line' => data_get($data, 'subject_line'),
                    'email_body'   => data_get($data, 'email_body'),
                ],
                $formattedMembersList
            );

            if ($inviteMember === false) {
                return false;
            }

            return $inviteMember;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);
            return false;
        }
    }
}
