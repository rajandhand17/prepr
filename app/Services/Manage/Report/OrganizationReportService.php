<?php

namespace App\Services\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;
use App\Models\Organization;
use Carbon\Carbon;

class OrganizationReportService
{
    public function getPaginatedChallenges(Organization $organization): false|array
    {
        try {
            $data = $organization->challenges_count()
                ->whereSearchFilter(request()->get('search'))
                ->withCount('members')
                ->paginate(config('site-settings.pagination_lab_report_challenge'));

            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getOrganizationEngagements(Organization $organization): false|array
    {
        try {
            $organization->loadCount(['shares', 'members']);

            return [
                'views'               => $organization->views_count,
                'shares'              => $organization->shares_count,
                'saves'               => $organization->favourite_count,
                'skilled_verified'    => 0,
                'achievement_issued'  => 0,
                'invitation_accepted' => $organization->members_count,
                'invitation_declined' => $organization->invitation_decline_count,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getOrganizationMembers(Organization $organization, bool $paginate = true): false|array
    {
        try {
            $query = MemberManagement::query()
                ->where('module_id', $organization->id)
                ->where('module_type', '0')
                ->with(['organizationUser']);

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_lab_report_members'));
                $metadata = $this->prepareMetaData($data);

                if (!$metadata) {
                    return false;
                }

                return [
                    ...$metadata,
                    'list' => $data,
                ];
            } else {
                $data = $query->get();
            }

            return [
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getMembersWithModuleCompletion(Organization $organization, bool $paginate = true): false|array
    {
        try {
            $query = $organization->allMembers()
                ->with(['user' => function ($query) {
                    $query->withCount([
                        'labsProgress' => function ($subQuery) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            });
                        },
                        'labProgramsProgress' => function ($subQuery) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            });
                        },
                        'challengesProgress' => function ($subQuery) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            });
                        }, // COUNTING THE CHALLENGES RELATED TO THIS LAB
                        'challengePathsProgress' => function ($subQuery) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            });
                        },  // COUNTING THE CHALLENGE PATHS RELATED TO THIS LAB
                        'resourcesModulesProgresses' => function ($subQuery) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            });
                        },
                        'resourcesGroupsProgresses' => function ($subQuery) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            });
                        },
                        'resourcesCollectionsProgresses' => function ($subQuery) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            });
                        },
                    ]); // COUNTING THE RESOURCE MODULES RELATED TO THIS LAB
                }]);

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_lab_report'));

                $metadata = $this->prepareMetaData($data);

                if (!$metadata) {
                    return false;
                }

                return [
                    ...$metadata,
                    'list' => $data,
                ];
            } else {
                $data = $query->get();
            }

            return [
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getPaginatedChallengePath(Organization $organization): false|array
    {
        try {
            $data = $organization->challenge_paths_count()
                ->whereSearchFilter(request()->get('search'))
                ->withCount('challengePathProgress')
                ->paginate(config('site-settings.pagination_lab_report_challenge'));

            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceModule(Organization $organization): false|array
    {
        try {
            $data = $organization->resource_modules_count()
                ->whereSearchFilter(request()->get('search'))
                ->withCount('resourceProgress')
                ->paginate(config('site-settings.pagination_lab_report_challenge'));

            $metadata = $this->prepareMetaData($data);

            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedLabs(Organization $organization): false|array
    {
        try {
            $data = $organization->labs()
                ->whereSearchFilter(request()->get('search'))
                ->withCount('members')
                ->paginate(config('site-settings.pagination_lab_report_challenge'));
            $metadata = $this->prepareMetaData($data);
            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedLabPrograms(Organization $organization): false|array
    {
        try {
            $data = $organization
                ->lab_programs_count()
                ->whereSearchFilter(request()->get('search'))
                ->withCount('labProgramProgress')
                ->paginate(config('site-settings.pagination_lab_report_challenge'));
            $metadata = $this->prepareMetaData($data);
            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceCollection(Organization $organization): false|array
    {
        try {
            $data = $organization->resource_collections_count()
                ->whereSearchFilter(request()->get('search'))
                ->withCount('resourceCollectionProgress')
                ->paginate(config('site-settings.pagination_lab_report_challenge'));
            $metadata = $this->prepareMetaData($data);
            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedResourceGroup(Organization $organization): false|array
    {
        try {
            $data = $organization->resource_groups_count()
                ->whereSearchFilter(request()->get('search'))
                ->withCount('resourceGroupProgress')
                ->paginate(config('site-settings.pagination_lab_report_challenge'));
            $metadata = $this->prepareMetaData($data);
            if (!$metadata) {
                return false;
            }

            return [
                ...$metadata,
                'list' => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    private function prepareMetaData($resource): false|array
    {
        try {
            return [
                'total_count'  => $resource->total(),
                'per_page'     => $resource->perPage(),
                'count'        => $resource->count(),
                'current_page' => $resource->currentPage(),
                'total_pages'  => $resource->lastPage(),
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param Organization $organization
     *
     * @return array|false
     */
    public function getDetailExportData(Organization $organization): array|false
    {
        try {
            $organizationEngagements = $this->getOrganizationEngagements($organization);
            $organizationMemberActivity = $this->organizationMemberActivity($organization);
            $organization = $organization->loadCount('labs', 'lab_programs_count', 'resource_modules_count', 'resource_collections_count', 'resource_groups_count', 'allMembers', 'challenges_count', 'challenge_paths_count');

            $arr = [
                ['Organization Title', $organization->display_name],
                ['Created on', $organization->created_at],
                ['location', $organization?->address?->first()?->full_address ?? '-'],
                ['Members Joined', $organization->all_members_count],
                [''],
                ['Components Overview'],
                ['Challenges', $organization->challenges_count_count],
                ['Challenge Paths', $organization->challenge_paths_count_count],
                ['Labs', $organization->labs_count],
                ['Lab Programs', $organization->lab_programs_count_count],
                ['Resource Modules', $organization->resource_modules_count_count],
                ['Resource Collections', $organization->resource_collections_count_count],
                ['Resource Groups', $organization->resource_groups_count_count],
                ['Projects', 0],
                [''],
                ['Organization Engagement'],
                ['Views', data_get($organizationEngagements, 'views', '0')],
                ['Discussion Posts', data_get($organizationEngagements, 'discussion_posts', 0)],
                ['Saves', data_get($organizationEngagements, 'saves', 0)],
                ['Saved & Started', data_get($organizationEngagements, 'saved_started', 0)],
                ['Share', data_get($organizationEngagements, 'share', 0)],
                ['Shared & Started', data_get($organizationEngagements, 'shared_started', 0)],
                ['Skill Verified', data_get($organizationEngagements, 'skills_verified', 0)],
                [''],
                ['Member Activity - Last 4 Weeks'],
                ['Date', 'Active', 'Inactive'],
            ];

            foreach ($organizationMemberActivity['last_6_months']['active'] as $index => $activeData) {
                $inactiveData = $organizationMemberActivity['last_6_months']['inactive'][$index];
                $arr[] = [
                    $activeData['label'],
                    $activeData['value'],
                    $inactiveData['value'],
                ];
            }

            return $arr;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

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
            $members = $organization->allMembers()->with([
                'user' => function ($query) {
                    $query->with(['userActivities' => function ($query) {
                        $query->where('activity_type', 'login');
                    }]);
                },
            ])->get();

            $userActivities = $members->pluck('user.userActivities')->flatten();
            $totalUsers = $members->count();

            return [
                'last_7_days'   => $this->getMemberActivityLast7Days($userActivities, $totalUsers),
                'last_4_weeks'  => $this->getMemberActivityLast4Weeks($userActivities, $totalUsers),
                'last_6_months' => $this->getMemberActivityLast6Months($userActivities, $totalUsers),
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    private function getMemberActivityLast7Days($userActivities, $totalUsers): false|array
    {
        try {
            $startDate = Carbon::now()->subDays(7)->startOfDay();
            $endDate = Carbon::now()->subDay()->startOfDay();

            $activeUsersData = $this->countActivitiesByDate($userActivities, $startDate, $endDate)
                ->keyBy('label');

            $allDates = collect();
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $label = $date->format('M d');
                $activeCount = $activeUsersData->get($label)['value'] ?? 0;
                $inactiveCount = $totalUsers - $activeCount;

                $allDates->push([
                    'label'    => $label,
                    'active'   => $activeCount,
                    'inactive' => $inactiveCount,
                ]);
            }

            return [
                'active'   => $allDates->map(fn ($item) => ['label' => $item['label'], 'value' => $item['active']])->values(),
                'inactive' => $allDates->map(fn ($item) => ['label' => $item['label'], 'value' => $item['inactive']])->values(),
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    private function getMemberActivityLast4Weeks($userActivities, $totalUsers): false|array
    {
        try {
            $startDate = Carbon::now()->subWeeks(4)->startOfDay();
            $endDate = Carbon::now()->subDay()->startOfDay();

            // Get active users data and initialize a map of all dates in 4-day intervals
            $activeUsersData = $this->countActivitiesByDate($userActivities, $startDate, $endDate)
                ->keyBy('label');

            $allDates = collect();
            for ($date = $startDate; $date->lte($endDate); $date->addDays(4)) {
                $label = $date->format('M d');
                $activeCount = $activeUsersData->get($label)['value'] ?? 0;
                $inactiveCount = $totalUsers - $activeCount;

                $allDates->push([
                    'label'    => $label,
                    'active'   => $activeCount,
                    'inactive' => $inactiveCount,
                ]);
            }

            return [
                'active'   => $allDates->map(fn ($item) => ['label' => $item['label'], 'value' => $item['active']])->values(),
                'inactive' => $allDates->map(fn ($item) => ['label' => $item['label'], 'value' => $item['inactive']])->values(),
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    private function getMemberActivityLast6Months($userActivities, $totalUsers): false|array
    {
        try {
            $sixMonthsAgo = Carbon::now()->subMonths(6);
            $now = Carbon::now();

            $months = collect();
            for ($date = $sixMonthsAgo; $date->lte($now); $date->addMonth()) {
                $months->push($date->format('F'));
            }

            $activeUsersData6Months = $this->countActivitiesByMonth($userActivities, $sixMonthsAgo, $now->subDay());

            $activeUsersData6Months = $months->map(function ($month) use ($activeUsersData6Months) {
                $found = $activeUsersData6Months->firstWhere('label', $month);

                return [
                    'label' => $month,
                    'value' => $found['value'] ?? 0,
                ];
            });

            $inactiveUsersData6Months = $activeUsersData6Months->map(function ($item) use ($totalUsers) {
                $activeCount = (int) $item['value'];
                $inactiveCount = max($totalUsers - $activeCount, 0);

                return [
                    'label' => $item['label'],
                    'value' => $inactiveCount,
                ];
            })->values()->all();

            return [
                'active'   => $activeUsersData6Months,
                'inactive' => $inactiveUsersData6Months,
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
                ->map(function ($activities, $date) {
                    return [
                        'label' => Carbon::parse($date)->format('M j'),
                        'value' => $activities->count(),
                    ];
                })->values();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    private function countActivitiesByMonth($activities, $startDate, $endDate)
    {
        try {
            return $activities->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('Y-m');
                })
                ->map(function ($activities, $month) {
                    return [
                        'label' => Carbon::parse($month.'-01')->format('M'),
                        'value' => $activities->count(),
                    ];
                })->values();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
