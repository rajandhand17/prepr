<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\DashboardLayout;
use Exception;
use Illuminate\Http\Request;

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

            $fetchUserDashboardLayout = DashboardLayout::where(['user_id' => $userData->id, 'dashboard_type' => $dashboardValue])->orderby('position_index', 'ASC')->get();

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

    public function storeStaticDefaultLayout($userData, $dashboardType)
    {
        try {
            switch ($dashboardType) {
                case 'user':
                    $card_type = ['reports', 'continue-left', 'deadlines', 'leaderboard', 'achievement', 'my-challenges', 'my-labs', 'my-projects', 'my-resources', 'inbox-friends', 'recommendations'];
                    $is_active = ['yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no'];
                    $position_index = ['0', '1', '2', '3', '4', '5', '6', '7'];
                    break;

                case 'lab':
                    $card_type = ['reports', 'subscription', 'deadlines', 'leaderboard', 'my-challenges', 'my-labs', 'my-projects', 'my-resources', 'inbox-friends', 'recommendations'];
                    $is_active = ['yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no'];
                    $position_index = ['0', '1', '2', '3', '4', '5', '6'];
                    break;

                case 'organization':
                    $card_type = ['reports', 'subscription', 'deadlines', 'leaderboard', 'my-organizations', 'my-challenges', 'my-labs', 'my-projects', 'my-resources', 'inbox-friends', 'recommendations'];
                    $is_active = ['yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no'];
                    $position_index = ['0', '1', '2', '3', '4', '5', '6', '7'];
                    break;
            }

            $staticValue = ['card_type' => $card_type, 'is_active' => $is_active, 'position_index' => $position_index];
            $request = new Request($staticValue);
            $storeStaticDefaultLayout = self::updateDashboardLayout($request, $userData, $dashboardType);
            if ($storeStaticDefaultLayout) {
                $fetchDashboardLayout = $this->fetchDashboardLayout($userData, $dashboardType);

                return $fetchDashboardLayout;
            }

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
