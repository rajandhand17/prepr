<?php

namespace App\Repositories\Api\Public\Organization;

use App\Helpers\UtilityHelper;
use App\Models\Organization;
use App\Services\Public\OrganizationService;
use App\Services\Public\OrganizationSocialActivitiesService;
use Carbon\Carbon;

class OrganizationRepository implements OrganizationInterface
{
    private $organizationService;
    private $organizationSocialActivitiesService;

    public function __construct(OrganizationService $organizationService, OrganizationSocialActivitiesService $organizationSocialActivitiesService)
    {
        $this->organizationService = $organizationService;
        $this->organizationSocialActivitiesService = $organizationSocialActivitiesService;
    }

    public function getList($request)
    {
        try {
            return $this->organizationService->getList($request);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getOrganizationBasedOnSlug($slug)
    {
        try {
            return $this->organizationService->getOrganizationBasedOnSlug($slug);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getColumnNameValue($action)
    {
        try {
            return $this->organizationSocialActivitiesService->getColumnNameValue($action);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function checkSocialActivity($organization_id, $column, $action)
    {
        try {
            return $this->organizationSocialActivitiesService->checkSocialActivity($organization_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function captureSocialActivity($organization_id, $column, $action)
    {
        try {
            return $this->organizationSocialActivitiesService->captureSocialActivity($organization_id, $column, $action);
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function incrementView(Organization $organization)
    {
        try {
            return $this->organizationService->incrementView($organization);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    /**
     * @param Organization $organization
     *
     * @return array[]|false
     */
    public function organizationMemberActivity(Organization $organization): array|false
    {
        try {
            $sevenDaysAgo = Carbon::now()->subDays(7);
            $fourWeeksAgo = Carbon::now()->subWeeks(4);
            $sixMonthsAgo = Carbon::now()->subMonths(6);

            $members = $organization->allMembers()->with([
                'user' => function ($query) {
                    $query->with(['userActivities' => function ($query) {
                        $query->where('activity_type', 'login');
                    }]);
                },
            ])->get();

            $userActivities = $members->pluck('user.userActivities')->flatten();
            $totalUsers = $members->count();

            // Active users count grouped by date for the last 7 days, 4 weeks, and 6 months
            $activeUsersData7Days = $this->countActivitiesByDate($userActivities, $sevenDaysAgo, now());
            $activeUsersData4Weeks = $this->countActivitiesByDate($userActivities, $fourWeeksAgo, now());
            $activeUsersData6Months = $this->countActivitiesByDate($userActivities, $sixMonthsAgo, now());

            // Calculate inactive users by subtracting active users from total users
            $inactiveUsersData7Days = $activeUsersData7Days->mapWithKeys(function ($count, $date) use ($totalUsers) {
                return [$date => $totalUsers - $count];
            });

            $inactiveUsersData4Weeks = $activeUsersData4Weeks->mapWithKeys(function ($count, $date) use ($totalUsers) {
                return [$date => $totalUsers - $count];
            });

            $inactiveUsersData6Months = $activeUsersData6Months->mapWithKeys(function ($count, $date) use ($totalUsers) {
                return [$date => $totalUsers - $count];
            });

            return [
                'last_7_days' => [
                    'active'   => $activeUsersData7Days,
                    'inactive' => $inactiveUsersData7Days,
                ],
                'last_4_weeks' => [
                    'active'   => $activeUsersData4Weeks,
                    'inactive' => $inactiveUsersData4Weeks,
                ],
                'last_6_months' => [
                    'active'   => $activeUsersData6Months,
                    'inactive' => $inactiveUsersData6Months,
                ],
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    private function countActivitiesByDate($activities, $startDate, $endDate)
    {
        try {
            return $activities->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('Y-m-d');
                })
                ->map(function ($activities) {
                    return $activities->count();
                });
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
