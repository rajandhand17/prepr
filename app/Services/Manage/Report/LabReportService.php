<?php

namespace App\Services\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Models\Lab;
use App\Models\User;
use Carbon\Carbon;

class LabReportService
{
    /**
     * @param Lab $lab
     *
     * @return false|array
     */
    public function getLabEngagement(Lab $lab): false|array
    {
        try {
            if (!$lab) {
                return false;
            }

            $lab->loadCount(['shares', 'skills', 'discussions']);

            /**
             * NUMBER OF USER THAT HAS SAVED AND STARTED IN THIS LAB.
             */
            $savedAndStarted = User::query()->select('id')->where(function ($query) use ($lab) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($lab) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $lab->id)->where(function ($query) {
                        $query->where('is_completed', '=', '1')->orWhereIn('status', ['1', '2']);
                    });
                })->whereHas('socialActivityLabs', function ($query) use ($lab) {
                    $query->where('lab_id', '=', $lab->id)->where('favourite', '=', '1');
                });
            })->count();

            /**
             * NUMBER OF USER THAT HAS SHARED AND STARTED IN THIS LAB.
             */
            $sharedAndStarted = User::query()->select('id')->where(function ($query) use ($lab) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($lab) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $lab->id)->where(function ($query) {
                        $query->where('is_completed', '=', '1')->orWhereIn('status', ['1', '2']);
                    });
                })->whereHas('socialActivityLabs', function ($query) use ($lab) {
                    $query->where('lab_id', '=', $lab->id)->where('share', '=', '1');
                });
            })->count();

            return [
                'views'            => $lab->views_count,
                'discussion_posts' => $lab->discussions_count,
                'saves'            => $lab->favouriteCount() ?? 0,
                'share'            => $lab->shares_count,
                'saved_started'    => $savedAndStarted,
                'shared_started'   => $sharedAndStarted,
                'skills_verified'  => $lab->skills_count,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function labMemberProgress(Lab $lab): array|false
    {
        try {
            $lab->load(['members.user.moduleCompletionStatus'])->loadCount('members');
            $notStarted = $lab->members()->whereHas('user', function ($query) use ($lab) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($lab) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $lab->id)->where(function ($query) {
                        $query->where('status', '=', '0');
                    });
                })->orWhereDoesntHave('moduleCompletionStatus', function ($query) use ($lab) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $lab->id);
                });
            })->count();

            $inProgress = $lab->members()->whereHas('user', function ($query) use ($lab) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($lab) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $lab->id)->where(function ($query) {
                        $query->where('status', '=', '1');
                    });
                });
            })->count();

            $completed = $lab->members()->whereHas('user', function ($query) use ($lab) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($lab) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $lab->id)->where(function ($query) {
                        $query->where('status', '=', '2');
                    });
                });
            })->count();

            return [
                'not_started' => $notStarted,
                'in_progress' => $inProgress,
                'completed'   => $completed,
                'total'       => $notStarted + $inProgress + $completed,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param Lab $lab
     *
     * @return array[]|false
     */
    public function labMemberActivity(Lab $lab): array|false
    {
        try {
            $members = $lab->allMembers()->with([
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

    private function getMemberActivityLast7Days($userActivities, $totalUsers): array|false
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

    private function getMemberActivityLast4Weeks($userActivities, $totalUsers): array|false
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

    private function getMemberActivityLast6Months($userActivities, $totalUsers): array|false
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

    /**
     * @param Lab $lab
     *
     * @return false|array
     */
    public function getPaginatedChallenges(Lab $lab): false|array
    {
        try {
            $data = $lab->challenges()
                ->whereSearchFilter(request()->get('search'))
                ->where('is_accessible', '1')
                ->withCount('members')
                ->paginate(config('site-settings.pagination_lab_report'));

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

    /**
     * @param Lab $lab
     *
     * @return false|array
     */
    public function getPaginatedResourceModules(Lab $lab): false|array
    {
        try {
            $data = $lab->resourceModules()
                ->whereSearchFilter(request()->get('search'))
                ->where('is_accessible', '1')
                ->withCount('resourceProgress')
                ->paginate(config('site-settings.pagination_lab_report'));

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

    /**
     * @param Lab $lab
     *
     * @return false|array
     */
    public function getPaginatedResourceCollections(Lab $lab): false|array
    {
        try {
            $data = $lab->resourceCollections()
                ->whereSearchFilter(request()->get('search'))
                ->where('is_accessible', '1')
                ->withCount('resourceCollectionProgress')
                ->paginate(config('site-settings.pagination_lab_report'));

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

    /**
     * @param Lab $lab
     *
     * @return false|array
     */
    public function getPaginatedResourceGroups(Lab $lab): false|array
    {
        try {
            $data = $lab->resourceGroups()
                ->whereSearchFilter(request()->get('search'))
                ->where('is_accessible', '1')
                ->withCount('resourceGroupProgress')
                ->paginate(config('site-settings.pagination_lab_report'));

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

    /**
     * @param Lab $lab
     *
     * @return false|array
     */
    public function getPaginatedChallengePaths(Lab $lab): false|array
    {
        try {
            $data = $lab->challengePaths()
                ->whereSearchFilter(request()->get('search'))
                ->where('is_accessible', '1')
                ->paginate(config('site-settings.pagination_lab_report'));

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

    /**
     * @param Lab $lab
     *
     * @return false|array
     */
    public function getPaginatedAchievements(Lab $lab): false|array
    {
        try {
            $data = $lab->achievement()
                ->paginate(config('site-settings.pagination_lab_report_achievement'));

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

    /**
     * @param Lab  $lab
     * @param bool $paginate
     *
     * @return false|array
     */
    public function getPaginatedMembers(Lab $lab, bool $paginate = true): false|array
    {
        try {
            $query = $lab->allMembers()
                ->with(['user' => function ($query) use ($lab) {
                    $query->with([
                        'userAchievements' => function ($subQuery) use ($lab) {
                            $subQuery->select('title', 'user_id')->where([
                                ['module_id', $lab->id],
                                ['achievement_type', '0'],
                            ]);
                        },
                        'labsProgress' => function ($subQuery) use ($lab) {
                            $subQuery->where('module_id', $lab->id);
                        },
                    ])->withCount([
                        'challengesProgress' => function ($subQuery) use ($lab) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            })->whereHas('challenge', function ($query) use ($lab) {
                                $query->whereRelation('labs', 'labs.id', '=', $lab->id);
                            });
                        }, // COUNTING THE CHALLENGES RELATED TO THIS LAB
                        'challengePathsProgress' => function ($subQuery) use ($lab) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            })->whereHas('challengePath', function ($query) use ($lab) {
                                $query->whereRelation('labs', 'labs.id', '=', $lab->id);
                            });
                        },  // COUNTING THE CHALLENGE PATHS RELATED TO THIS LAB
                        'resourcesModulesProgresses' => function ($subQuery) use ($lab) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            })->whereHas('resourceModule', function ($query) use ($lab) {
                                $query->whereRelation('labs', 'labs.id', '=', $lab->id);
                            });
                        },
                        'resourcesGroupsProgresses' => function ($subQuery) use ($lab) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            })->whereHas('resourceGroup', function ($query) use ($lab) {
                                $query->whereRelation('labs', 'labs.id', '=', $lab->id);
                            });
                        },
                        'resourcesCollectionsProgresses' => function ($subQuery) use ($lab) {
                            $subQuery->where(function ($query) {
                                $query->where('percentage', '=', '100')
                                    ->orWhere('is_completed', '=', '1')
                                    ->orWhere('status', '=', '2');
                            })->whereHas('resourceCollection', function ($query) use ($lab) {
                                $query->whereRelation('labs', 'labs.id', '=', $lab->id);
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
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param Lab $lab
     *
     * @return array|false
     */
    public function getDetailExportData(Lab $lab): array|false
    {
        try {
            $labEngagements = $this->getLabEngagement($lab);
            $labMemberProgress = $this->labMemberProgress($lab);
            $labAchievement = $lab->achievement;
            $labMemberActivity = $this->labMemberActivity($lab);
            $lab = $lab->loadCount('challenges', 'challengePaths', 'resourceModules', 'resourceCollections', 'resourceGroups', 'members');

            $arr = [
                ['Lab Title', $lab->title],
                ['Privacy', $lab->formattedLabPrivacy],
                ['Pricing', ''],
                ['Level', data_get($lab->formattedLabLevel, 'title')],
                ['Duration', data_get($lab->formattedLabDuration, 'title')],
                ['Members Joined', $lab->members_count],
                [''],
                ['Components Overview'],
                ['Challenges', $lab->challenges_count],
                ['Challenge Paths', $lab->challenge_paths_count],
                ['Resource Modules', $lab->resource_modules_count],
                ['Resource Collections', $lab->resource_collections_count],
                ['Resource Groups', $lab->resource_groups_count],
                [''],
                ['Lab Member Progress', ''],
                ['Not Started', data_get($labMemberProgress, 'not_started', 0)],
                ['In Progress', data_get($labMemberProgress, 'in_progress', 0)],
                ['Completed', data_get($labMemberProgress, 'completed', 0)],
                [''],
                ['Associated Achievements'],
                ['Badges', '1'],
                ['Points', $labAchievement?->achievement_points ?? 0],
                [''],
                ['Achievements Details'],
                ['Title', 'Badges', 'Points'],
                [$labAchievement->achievement_name ?? '-', $labAchievement->achievement_image ?? '-', $labAchievement->achievement_points ?? '-'],
                [''],
                ['Lab Engagement'],
                ['Views', data_get($labEngagements, 'views', '0')],
                ['Discussion Posts', data_get($labEngagements, 'discussion_posts', 0)],
                ['Saves', data_get($labEngagements, 'saves', 0)],
                ['Saved & Started', data_get($labEngagements, 'saved_started', 0)],
                ['Share', data_get($labEngagements, 'share', 0)],
                ['Shared & Started', data_get($labEngagements, 'shared_started', 0)],
                ['Skill Verified', data_get($labEngagements, 'skills_verified', 0)],
                [''],
                ['Member Activity - Last 4 Weeks'],
                ['Date', 'Active', 'Inactive'],
            ];

            foreach ($labMemberActivity['last_4_weeks']['active'] as $index => $activeData) {
                $inactiveData = $labMemberActivity['last_4_weeks']['inactive'][$index];
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
     * @param $resource
     *
     * @return array|false
     */
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
}
