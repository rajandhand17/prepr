<?php

namespace App\Services\GO1;

use App\Models\MemberManagement;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class GO1PermissionService
{

    private function isUserBelongToPrepr()
    {
        try {
            $user = MemberManagement::query()
                ->where('module_id', config('go1.prepr_id'))
                ->where('module_type', config('constants.member_management_component_type.organization'))
                ->where('email', auth()->user()->email)
                ->first();

            if (!$user) {
                return false;
            }

            return true;
        } catch (\Exception $exception) {
            Log::error($exception);
            return false;
        }
    }

    public function canPlayGO1Resoruces()
    {
        try {
            if (auth()->user()->hasRole('super_admin')) {
                return true;
            }

            return $this->isUserBelongToPrepr();
        } catch (Exception $exception) {
            Log::error($exception);
            return false;
        }
    }

    public function canCreateGO1Resource()
    {
        try {
            if (auth()->user()->hasRole('super_admin')) {
                return true;
            }

            $isPreprUser = $this->isUserBelongToPrepr();

            if (!$isPreprUser) {
                return false;
            }

            return auth()->user()->hasRole(['resource_manager', 'organization_manager', 'organization_owner']);
        } catch (\Exception $exception) {
            Log::error($exception);
            return false;
        }
    }

}
