<?php

namespace App\Traits;

use App\Models\MemberManagement;
use App\Models\User;

trait AuthUserTrait
{
    /**
     * @var array|string[]
     */
    protected array $adminRoles = ['super_admin'];

    /**
     * @var array
     */
    protected array $preprGlobalSearchAllowedRoles = ['organization_owner', 'organization_manager'];

    /**
     * @param User|null $user
     *
     * @return bool
     */
    public function allowedGlobalSearch(User|null $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        // IF THE USER IS SUPER ADMIN CAN SEE GLOBAL USERS
        if ($user->hasRole($this->adminRoles)) {
            return true;
        }

        // LIST OF ORGANIZATION IDS THAT THE USER IS THE MEMBER OF
        $userOrganizationIds = $this->getUserOrganizationIds($user);
        $preprOrganizationId = config('go1.go1_prepr_id');

        // IF THE USER IS PREPR - USER
        if (in_array($preprOrganizationId, $userOrganizationIds)) {
            return $user->hasRole($this->preprGlobalSearchAllowedRoles);
        }

        return false;
    }

    /**
     * @param User|null $user
     *
     * @return array
     */
    public function getUserOrganizationIds(User|null $user = null): array
    {
        $user = $user ?? auth()->user();
        if (data_get($user, 'email')) {
            return MemberManagement::query()->where([
                'module_type'   => '0',
                'invite_status' => '1',
                'email'         => data_get($user, 'email'),
            ])->get()->pluck('module_id')->unique()->toArray();
        }

        return [];
    }
}
