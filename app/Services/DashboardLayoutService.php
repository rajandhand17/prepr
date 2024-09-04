<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\DashboardLayout;
use Exception;

class DashboardLayoutService
{
    public function fetchDashboardLayout($userData, $dashboardType)
    {
        try {
            switch ($dashboardType) {
                case 'user':
                    $dashboardValue = config('constants.dashboard_type.user');
                    break;

                case 'lab':
                    $dashboardValue = config('constants.dashboard_type.lab');
                    break;

                case 'organization':
                    $dashboardValue = config('constants.dashboard_type.organization');
                    break;
            }

            $fetchUserDashboardLayout = DashboardLayout::where(['user_id' => $userData->id, 'dashboard_type' => $dashboardValue])->get();

            return $fetchUserDashboardLayout;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateDashboardLayout($request, $userData, $dashboardType)
    {
        try {
            switch ($dashboardType) {
                case 'user':
                    $dashboardValue = config('constants.dashboard_type.user');
                    break;

                case 'lab':
                    $dashboardValue = config('constants.dashboard_type.lab');
                    break;

                case 'organization':
                    $dashboardValue = config('constants.dashboard_type.organization');
                    break;
            }

            DashboardLayout::where(['user_id' => $userData->id, 'dashboard_type' => $dashboardValue])->delete();
            foreach ($request->card_type as $key => $value) {
                switch ($request['card_type'][$key]) {
                    case 'reports':
                        $cardType = config('constants.dashboard_card_type.reports');
                        break;

                    case 'deadlines':
                        $cardType = config('constants.dashboard_card_type.deadlines');
                        break;

                    case 'leaderboard':
                        $cardType = config('constants.dashboard_card_type.leaderboard');
                        break;

                    case 'my-challenges':
                        $cardType = config('constants.dashboard_card_type.my-challenges');
                        break;

                    case 'my-labs':
                        $cardType = config('constants.dashboard_card_type.my-labs');
                        break;

                    case 'my-projects':
                        $cardType = config('constants.dashboard_card_type.my-projects');
                        break;

                    case 'my-resources':
                        $cardType = config('constants.dashboard_card_type.my-resources');
                        break;

                    case 'my-organizations':
                        $cardType = config('constants.dashboard_card_type.my-organizations');
                        break;

                    case 'subscription':
                        $cardType = config('constants.dashboard_card_type.subscription');
                        break;

                    case 'inbox-friends':
                        $cardType = config('constants.dashboard_card_type.inbox-friends');
                        break;

                    case 'recommendations':
                        $cardType = config('constants.dashboard_card_type.recommendations');
                        break;

                    case 'continue-left':
                        $cardType = config('constants.dashboard_card_type.continue-left');
                        break;

                    case 'achievement':
                        $cardType = config('constants.dashboard_card_type.achievement');
                        break;
                }

                switch ($request['is_active'][$key]) {
                    case 'yes':
                        $isActive = '0';
                        break;

                    case 'no':
                        $isActive = '1';
                        break;
                }

                $updateLayout = new DashboardLayout();
                $updateLayout->user_id = $userData->id;
                $updateLayout->dashboard_type = $dashboardValue;
                $updateLayout->card_type = $cardType;
                $updateLayout->is_active = $isActive;
                $updateLayout->position_index = $request['position_index'][$key] ?? null;
                $updateLayout->save();
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
