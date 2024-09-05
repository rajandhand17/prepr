<?php

namespace App\Services\Manage\Report;

use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use SebastianBergmann\ObjectEnumerator\Exception;

class ChallengeReportService
{
    public function getChallengeMemberProgress(Challenge $challenge): false|array
    {
        try {
            /**
             * LAZY LOADING.
             */
            $challenge->load(['challengeProgress', 'members.user.moduleCompletionStatus'])->loadCount('members', 'challengeProgress');
            $challengeDates = $challenge->challenge_timelines;

            /**
             * ALL PROGRESS.
             */
            $allProgress = $challenge->challengeProgress()->get();

            $notStarted = $challenge->members()->whereHas('user', function ($query) use ($challenge) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($challenge) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $challenge->id)->where(function ($query) {
                        $query->where('status', '=', '0');
                    });
                })->orWhereDoesntHave('moduleCompletionStatus', function ($query) use ($challenge) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $challenge->id);
                });
            })->count();

            $inProgress = $challenge->members()->whereHas('user', function ($query) use ($challenge) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($challenge) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $challenge->id)->where(function ($query) {
                        $query->where('status', '=', '1');
                    });
                });
            })->count();

            $completed = $challenge->members()->whereHas('user', function ($query) use ($challenge) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($challenge) {
                    $query->where('module_type', '=', '0')->where('module_id', '=', $challenge->id)->where(function ($query) {
                        $query->where('status', '=', '2');
                    });
                });
            })->count();

            $late_submission = $allProgress->filter(function ($project) use ($challengeDates) {
                return $project->is_submitted && (data_get($challengeDates, 'submission_deadline_date') && Carbon::parse($challengeDates->submission_deadline_date)->lessThanOrEqualTo(now()));
            })->count();
            $deadline_missed = $allProgress->filter(function ($project) use ($challengeDates) {
                return !$project->is_submitted && (data_get($challengeDates, 'flexible_expire_deadline') && Carbon::parse($challengeDates->flexible_expire_deadline)->lessThanOrEqualTo(now()));
            })->count();

            return [
                'not_started'     => $notStarted,
                'in_progress'     => $inProgress,
                'completed'       => $completed,
                'total'           => $challenge->challenge_progress_count,
                'late_submission' => $late_submission,
                'deadline_missed' => $deadline_missed,
            ];
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param Challenge $challenge
     *
     * @return false|array
     */
    public function getChallengeEngagement(Challenge|null $challenge): false|array
    {
        try {
            if (!$challenge) {
                return false;
            }

            $challenge->loadCount(['shares', 'skills', 'discussions']);

            /**
             * NUMBER OF USER THAT HAS SAVED AND STARTED IN THIS CHALLENGE.
             */
            $savedAndStarted = User::query()->select('id')->where(function ($query) use ($challenge) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($challenge) {
                    $query->where('module_type', '=', '2')->where('module_id', '=', $challenge->id)->where(function ($query) {
                        $query->where('is_completed', '=', '1')->orWhereIn('status', ['1', '2']);
                    });
                })->whereHas('socialActivityChallenges', function ($query) use ($challenge) {
                    $query->where('challenge_id', '=', $challenge->id)->where('favourite', '=', '1');
                });
            })->count();

            /**
             * NUMBER OF USER THAT HAS SHARED AND STARTED IN THIS CHALLENGE.
             */
            $sharedAndStarted = User::query()->select('id')->where(function ($query) use ($challenge) {
                $query->whereHas('moduleCompletionStatus', function ($query) use ($challenge) {
                    $query->where('module_type', '=', '2')->where('module_id', '=', $challenge->id)->where(function ($query) {
                        $query->where('is_completed', '=', '1')->orWhereIn('status', ['1', '2']);
                    });
                })->whereHas('socialActivityChallenges', function ($query) use ($challenge) {
                    $query->where('challenge_id', '=', $challenge->id)->where('share', '=', '1');
                });
            })->count();

            return [
                'views'            => $challenge->views_count,
                'discussion_posts' => $challenge->discussions_count,
                'saves'            => $challenge->favouriteCount() ?? 0,
                'share'            => $challenge->shares_count,
                'saved_started'    => $savedAndStarted,
                'shared_started'   => $sharedAndStarted,
                'skills_verified'  => $challenge->skills_count,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function getPaginatedLabs(Challenge $challenge, bool $paginate = true): array|false
    {
        try {
            $query = $challenge->labs()
                ->whereSearchFilter(request()->get('keyword'))
                ->withCount('members');

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_per_page'));

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

    public function getPaginatedLabPrograms(Challenge $challenge, bool $paginate = true): array|false
    {
        try {
            $query = $challenge->labPrograms()
                ->whereSearchFilter(request()->get('keyword'));

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_per_page'));
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

    public function getPaginatedResourceModules(Challenge $challenge, bool $paginate = true): array|false
    {
        try {
            $query = $challenge->resourceModules()
                ->whereSearchFilter(request()->get('keyword'))
                ->withCount('resourceProgress');

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_per_page'));
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

    public function getPaginatedResourceCollections(Challenge $challenge, bool $paginate = true): false|array
    {
        try {
            $query = $challenge->resourceCollections()
                ->whereSearchFilter(request()->get('keyword'))
                ->withCount('resourceCollectionProgress');

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_per_page'));
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

    public function getPaginatedResourceGroups(Challenge $challenge, bool $paginate = true): false|array
    {
        try {
            $query = $challenge->resourceGroups()
                ->whereSearchFilter(request()->get('keyword'))
                ->withCount('resourceGroupProgress');

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_per_page'));
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
     * @param Challenge $challenge
     *
     * @return false|array
     */
    public function getPaginatedAchievements(Challenge $challenge, bool $paginate = true): false|array
    {
        try {
            $query = $challenge->achievements();

            if ($paginate) {
                $data = $query->paginate(config('site-settings.pagination_lab_report_achievement'));
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
     * @param $challenge
     *
     * @return false|array
     */
    public function getPaginatedMembers(Challenge $challenge, bool $paginate = true): false|array
    {
        try {
            $query = $challenge->allMembers()
                ->with(['user' => function ($query) use ($challenge) {
                    $query->with([
                        'userAchievements' => function ($subQuery) use ($challenge) {
                            $subQuery->select('title', 'user_id')->where([
                                ['module_id', $challenge->id],
                                ['achievement_type', '2'],
                            ]);
                        },
                        'userProjects' => function ($subQuery) use ($challenge) {
                            $subQuery->where('challenge_id', $challenge->id)
                                ->withCount('getProjectImages', 'getProjectVideos', 'getProjectDocs');
                        },
                        'challengeDiscussions' => function ($subQuery) use ($challenge) {
                            $subQuery->where('module_id', $challenge->id)->count();
                        },
                    ])->withCount(['challengeDiscussions' => function ($subQuery) use ($challenge) {
                        $subQuery->where('module_id', $challenge->id);
                    }]);
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
     * @param Challenge $challenge
     *
     * @return array[]|false
     */
    public function challengeMemberActivity(Challenge $challenge): array|false
    {
        try {
            $members = $challenge->allMembers()->with([
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
     * @param      $challenge
     * @param bool $paginate
     *
     * @return false|array
     */
    public function getPaginatedAssessments($challenge, bool $paginate = true): false|array
    {
        try {
            if (!$challenge->challenge_assessment) {
                return [
                    'assessor'                   => 0,
                    'project_assessed'           => 0,
                    'project_pending_assignment' => 0,
                    'winner_selected'            => 0,
                    'list'                       => [],
                ];
            }

            $query = $challenge->challenge_assessment->projects()
                ->whereAssessment(request()->input('assessment_type') ?? '');

            $data = $paginate ? $query->paginate(config('site-settings.pagination_lab_report')) : $query->get();

            $counts = $challenge->challenge_assessment()->withCount([
                'projects as assessed_count' => function ($query) {
                    $query->whereHas('challengeAssessmentUsers');
                },
                'projects as need_to_assess_count' => function ($query) {
                    $query->whereDoesntHave('challengeAssessmentUsers');
                },
            ])->first();

            $metadata = $paginate ? $this->prepareMetaData($data) : [];

            return [
                ...$metadata,
                'assessor'                   => $counts->assessed_count + $counts->need_to_assess_count,
                'project_assessed'           => $counts->assessed_count,
                'project_pending_assignment' => $counts->need_to_assess_count,
                'winner_selected'            => 0,
                'list'                       => $data,
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param Challenge $challenge
     *
     * @return array|false
     */
    public function getDetailExportData(Challenge $challenge): array|false
    {
        try {
            $challengeEngagements = $this->getChallengeEngagement($challenge);
            $challengeMemberActivity = $this->challengeMemberActivity($challenge);
            $challengeAssociatedProjects = $this->getChallengeAssociatedProjects($challenge);
            $challengeAchievements = $challenge->achievements;

            $challenge = $challenge->loadCount('labs', 'labPrograms', 'resourceModules', 'resourceCollections', 'resourceGroups', 'members', 'projects', 'submitted_projects');

            $arr = [
                ['Challenge Title', $challenge->title],
                ['Status', $challenge->formattedChallengeStatus],
                ['Deadline', $challenge->formatted_submission_deadline_date],
                ['Level', data_get($challenge->formattedChallengeLevel, 'title')],
                ['Duration', data_get($challenge->formattedChallengeDuration, 'title')],
                ['Privacy', $challenge->formattedChallengePrivacy],
                ['Pricing', ''],
                ['Members Joined', $challenge->members_count],
                ['Project Submission', $challenge->submitted_projects_count],
                ['Sponsors', $challenge->hosts()->count()],
                [''],
                ['Components Overview'],
                ['Labs', $challenge->labs_count],
                ['Lab Programs', $challenge->lab_programs_count],
                ['Resource Modules', $challenge->resource_modules_count],
                ['Resource Collections', $challenge->resource_collections_count],
                ['Resource Groups', $challenge->resource_groups_count],
                ['Projects', $challenge->resource_groups_count],
                [''],
                ['Associated Achievements'],
                ['Badges', $challenge->achievements->count()],
                ['Points', $challenge->achievement_points],
                [''],
                ['Achievements Details'],
                ['Title', 'Badges', 'Points'],
            ];

            if ($challengeAchievements) {
                foreach ($challengeAchievements as $achievement) {
                    $arr[] = [$achievement->achievement_name, $achievement->achievement_image, $achievement->achievement_points];
                }
            } else {
                $arr[] = ['-', '-', '-'];
            }

            $arr = array_merge(
                $arr,
                [
                    [''],
                    ['Associated Projects'],
                    ['Submitted', data_get($challengeAssociatedProjects, 'submitted_status.submitted')],
                    ['In Progress', data_get($challengeAssociatedProjects, 'submitted_status.in_progress')],
                    ['Late Submission', data_get($challengeAssociatedProjects, 'submitted_status.late_submission')],
                    ['Deadline Missed', data_get($challengeAssociatedProjects, 'submitted_status.deadline_missed')],
                    ['Need To Access', data_get($challengeAssociatedProjects, 'submitted_status.need_to_access')],
                    ['Assessed', data_get($challengeAssociatedProjects, 'project_assess_status.assessed')],
                    [''],
                    ['Challenge Engagement'],
                    ['Views', data_get($challengeEngagements, 'views', '0')],
                    ['Discussion Posts', data_get($challengeEngagements, 'discussion_posts', 0)],
                    ['Saves', data_get($challengeEngagements, 'saves', 0)],
                    ['Saved & Started', data_get($challengeEngagements, 'saved_started', 0)],
                    ['Share', data_get($challengeEngagements, 'share', 0)],
                    ['Shared & Started', data_get($challengeEngagements, 'shared_started', 0)],
                    ['Skill Verified', data_get($challengeEngagements, 'skills_verified', 0)],
                    [''],
                    ['Member Activity - Last 4 Weeks'],
                    ['Date', 'Active', 'Inactive'],
                ]
            );

            foreach ($challengeMemberActivity['last_6_months']['active'] as $index => $activeData) {
                $inactiveData = $challengeMemberActivity['last_6_months']['inactive'][$index];
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

    public function getComponentExportData(Challenge $challenge): array|false
    {
        try {
            $components = [
                'Labs'                 => $this->getPaginatedLabs($challenge, false),
                'Lab Programs'         => $this->getPaginatedLabPrograms($challenge, false),
                'Resource Modules'     => $this->getPaginatedResourceModules($challenge, false),
                'Resource Collections' => $this->getPaginatedResourceCollections($challenge, false),
                'Resource Groups'      => $this->getPaginatedResourceGroups($challenge, false),
            ];

            $values = [
                'Labs'                 => 'lab',
                'Lab Programs'         => 'lab-program',
                'Resource Modules'     => 'resource',
                'Resource Collections' => 'resource-collection',
                'Resource Groups'      => 'resource-group',
            ];

            $arr = [];

            foreach ($components as $componentName => $componentData) {
                $arr[] = [$componentName];

                if ($componentData['list']->isEmpty()) {
                    $arr[] = [sprintf('No %s is Associated', $componentName)];
                    $arr[] = [''];
                } else {
                    $arr[] = ['Title', 'URL', 'Last Updated'];
                    foreach ($componentData['list'] as $item) {
                        $arr[] = [
                            $item->title,
                            sprintf('%s/%s/%s', env('FRONTEND_SITE_URL'), $values[$componentName], $item->slug),
                            $item->updated_at->format('d F, Y'),
                        ];
                    }
                }

                $arr[] = [''];
            }

            return $arr;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param $challenge
     * @param $project_id
     *
     * @return false|array
     */
    public function getChallengeAssessmentDetail($challenge, $project_id): false|array
    {
        try {
            $data = $challenge->challenge_assessment()
                ->with(['projects' => function ($query) use ($project_id) {
                    $query->where('id', $project_id)
                        ->with(['users' => function ($query) use ($project_id) {
                            $query->with(['challengeAssessmentUsers.challengeAssessmentCriteria'])->where('project_id', $project_id);
                        }, 'getProjectAssessment']);
                }])
                ->first();

            if (!$data) {
                return [
                    'success' => false,
                    'message' => __('No assessments found.'),
                ];
            }

            return [
                'success' => true,
                'data'    => [
                    'title'        => $data->projects->first() ? $data->projects->first()->title : '-',
                    'score'        => '0/0',
                    'weight'       => '',
                    'team_members' => $data->member_names,
                    'achievement'  => 'Participation award',
                    'users'        => data_get($data->projects()->first(), 'users', []),
                ],
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    /**
     * @param $challenge
     *
     * @return false|array
     */
    public function getChallengeAssociatedProjects($challenge): false|array
    {
        try {
            $challengeDates = $challenge->challenge_timelines;
            $projects = $this->fetchProjectsBasedOnChallenge($challenge->id);

            if (empty($projects)) {
                return [
                    'submitted_status' => [
                        'submitted'       => 0,
                        'in_progress'     => 0,
                        'late_submission' => 0,
                        'deadline_missed' => 0,
                        'total'           => 0,
                    ],
                    'project_assess_status' => [
                        'assessed'       => 0,
                        'need_to_assess' => 0,
                        'total'          => 0,
                    ],
                ];
            }

            $assessedCount = $projects->filter(function ($project) {
                return $project->challengeAssessmentUsers()->exists();
            })->count();

            $needToAssessCount = $projects->filter(function ($project) {
                return !$project->challengeAssessmentUsers()->exists();
            })->count();

            $counts = [
                'submitted'       => $projects->where('is_submitted', 1)->count(),
                'in_progress'     => $projects->where('is_submitted', 0)->count(),
                'late_submission' => $projects->filter(function ($project) use ($challengeDates) {
                    return !$project->is_submitted &&
                        ($challengeDates?->submission_deadline_date &&
                            Carbon::parse($challengeDates->submission_deadline_date)->lessThanOrEqualTo(now()));
                })->count(),
                'deadline_missed' => $projects->filter(function ($project) use ($challengeDates) {
                    return !$project->is_submitted &&
                        ($challengeDates?->flexible_expire_deadline &&
                            Carbon::parse($challengeDates->flexible_expire_deadline)->lessThanOrEqualTo(now()));
                })->count(),
            ];

            return [
                'submitted_status' => [
                    ...$counts,
                    'total' => $counts['submitted'] + $counts['in_progress'],
                ],
                'project_assess_status' => [
                    'assessed'       => $assessedCount,
                    'need_to_assess' => $needToAssessCount,
                    'total'          => $assessedCount + $needToAssessCount,
                ],
            ];
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function fetchProjectsBasedOnChallenge($challengeId)
    {
        try {
            return Project::where('challenge_id', $challengeId)->get();
        } catch (Exception $e) {
            UtilityHelper::logError($e);

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
        } catch (Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }
}
