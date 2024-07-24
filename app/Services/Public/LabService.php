<?php

namespace App\Services\Public;

use App\Helpers\Airmeet\AirmeetEventHelper;
use App\Helpers\UtilityHelper;
use App\Models\ComponentAssociation;
use App\Models\Lab;
use App\Models\MemberManagement;
use App\Models\User;
use App\Services\Manage\LabSkillsGroupsStackService;
use App\Services\Manage\LabTagsGroupsService;
use Carbon\Carbon;

class LabService
{
    public function getList($request)
    {
        try {
            $lab_list = Lab::where('labs.status', '1')->where('labs.is_accessible', '1');
            $lab_list = self::filterLabList($request, $lab_list);

            return $lab_list->paginate(config('site-settings.pagination_per_page'));
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function filterLabList($request, $lab_list)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $lab_list = $lab_list->whereSearchFilter($request->search ?? '');
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $lab_list = $lab_list->whereIn('labs.category_id', $request->category);
            }
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $getOrganizationIds = OrganizationService::getOrganizationExistBasedOnUuidArray($request->organization_id)->pluck('id');
                $lab_list = $lab_list->whereIn('organization_id', $getOrganizationIds);
            }
            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $labIds = LabSocialActivitiesService::getLabsBasedOnActivity($activityType)->pluck('lab_id');
                $lab_list->whereIn('labs.id', $labIds);
            }
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $lab_list->orderBy('labs.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $lab_list->orderBy('labs.title', 'DESC');
                        break;
                    case 'creation_date':
                        $lab_list->orderBy('labs.created_at', 'ASC');
                        break;
                    default:
                        $lab_list->orderBy('labs.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $lab_list = $lab_list->where('labs.privacy', '0');
                        break;
                    case 'private':
                        $lab_list = $lab_list->where('labs.privacy', '1');
                        break;
                    default:
                        $lab_list = $lab_list;
                }
            }
            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $lab_list = $lab_list->whereIn('labs.id', function ($query) use ($request) {
                    $query->select('lab_skills_groups_stack.lab_id')
                        ->from('lab_skills_groups_stack')
                        ->whereIn('lab_skills_groups_stack.foreign_id', $request->skills)
                        ->where('lab_skills_groups_stack.type', '0')
                        ->whereNull('lab_skills_groups_stack.deleted_at')
                        ->distinct();
                })->distinct('labs.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $lab_list = $lab_list->whereIn('labs.id', function ($query) use ($request) {
                    $query->select('lab_tags_groups.lab_id')
                        ->from('lab_tags_groups')
                        ->whereIn('lab_tags_groups.foreign_id', $request->tags)
                        ->where('lab_tags_groups.type', '0')
                        ->whereNull('lab_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('labs.uuid');
            }
            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $lab_list = $lab_list->whereIn('duration_id', $request->duration_id);
            }
            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $lab_list = $lab_list->whereIn('level_id', $request->level_id);
            }
            if ($request->has('request_status') && !empty($request->request_status)) {
                if (auth('api')->check()) {
                    $status_array = ['accepted', 'pending', 'declined'];
                    if (in_array($request->request_status, $status_array)) {
                        $lab_list = $lab_list->join('member_management', 'labs.id', '=', 'member_management.module_id')
                            ->where(['member_management.module_type' => '1', 'member_management.email' => auth('api')->user()->email]);
                        switch ($request->request_status) {
                            case 'invited':
                                $lab_list->where('member_management.invite_status', '0');
                                break;
                            case 'accepted':
                                $lab_list->where('member_management.invite_status', '1');
                                break;
                            case 'pending':
                                $lab_list->where('member_management.invite_status', '2');
                                break;
                            case 'declined':
                                $lab_list->where('member_management.invite_status', '3');
                                break;
                            default:
                                $lab_list;
                        }
                    }
                }
            }

            return $lab_list;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabBasedOnSlug($slug)
    {
        try {
            return Lab::where('slug', $slug)->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getProjectLabs($request, $challengeId)
    {
        try {
            $getLabList = ComponentAssociation::where('challenge_id', $challengeId)->whereNotNull('lab_id')->get()->pluck('lab_id');

            $userEmail = auth()->user()->email;
            $labMemberIds = MemberManagement::whereIn('module_id', $getLabList)->where(['module_type' => '1', 'invite_status' => '1', 'email' => $userEmail])->pluck('module_id');
            $lab_list = Lab::select('uuid', 'title', 'media')->whereIn('id', $labMemberIds)->where('is_accessible', '1');
            $lab_list = self::filterLabList($request, $lab_list);
            $limit = config('site-settings.listing_limit');

            return $lab_list->limit($limit)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getTrendingLab()
    {
        try {
            $getLatestLabsIds = MemberManagementService::getLatestIdsBasedOnModule(config('constants.module_component_type.lab'));
            $lab_list = Lab::select()->where('labs.status', '1')->whereIn('id', $getLatestLabsIds);

            return $lab_list->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getLabsBasedOnSKillsAndTags($usersSkills, $tags)
    {
        try {
            /*gets Labs based on user skills*/
            $getLabsIdsBasedOnSKills = LabSkillsGroupsStackService::getLabIdBasesOnSKillsId($usersSkills);
            /*gets Tags based on user tags*/
            $getLabsIdsBasedOnTags = LabTagsGroupsService::getLabsIdBasedOnTagsId($tags);
            $labIds = $getLabsIdsBasedOnSKills->merge($getLabsIdsBasedOnTags)->unique();
            if (!empty($labIds)) {
                $lab = Lab::whereIn('labs.id', $labIds)->where('user_id', '!=', auth()->user()->id)->pluck('id')->take(config('site-settings.explore_page_limit_max'));
            } else {
                $lab = Lab::where('user_id', '!=', auth()->user()->id)->pluck('id')->take(config('site-settings.explore_page_limit_min'));
            }
            if (count($lab) < config('site-settings.explore_page_limit_max')) {
                $limit = config('site-settings.explore_page_limit_max') - count($lab);
                $labNewIds = Lab::where('user_id', '!=', auth()->user()->id)->whereNotIn('id', $lab)->pluck('id')->take($limit);
                $lab = $lab->merge($labNewIds)->unique();
            }

            return Lab::whereIn('id', $lab)->take(config('site-settings.explore_page_limit_max'))->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabsBasedOnIds($labIds)
    {
        try {
            $labList = Lab::whereIn('id', $labIds)->where('is_accessible', '1')->get();

            return $labList;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getLabBasedOnId($Id)
    {
        try {
            return Lab::select('id', 'uuid', 'title', 'media', 'slug', 'description')->where(['id' => $Id, 'is_accessible' => '1'])->first();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function canJoinLiveEvent(Lab $lab, User $user): bool
    {
        try {
            $joined = $lab->joined();

            return ($joined && $joined !== 'NA') || $user->hasPermission('can_join_live_event_lab');
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function sendLiveEventInvitationLinkToMembers(Lab $lab): bool
    {
        try {
            $eventId = data_get($lab->airMeet, 'airmeet_event_id');
            if ($eventId) {
                $lab->members()->get()->each(function (User $user) use ($eventId) {
                    AirmeetEventHelper::addAttendeeToEvent($eventId, [
                        [
                            'user_id'    => $user->id,
                            'email'      => data_get($user, 'email'),
                            'first_name' => data_get($user, 'first_name', data_get($user, 'full_name')),
                            'last_name'  => data_get($user, 'last_name'),
                        ],
                    ]);
                });
            }

            return true;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function liveEventDetails(Lab $lab)
    {
        try {
            $eventId = data_get($lab->airMeet, 'airmeet_event_id');
            if ($eventId) {
                $eventDetails = AirmeetEventHelper::getAirmeetEventInfo($eventId)->json();
                $sessions = data_get($eventDetails, 'sessions');

                /**
                 * CREATING A HASHMAP BASED ON DATE FOR FRONTEND.
                 */
                $sessionFormatted = collect($sessions)->map(function ($value) {
                    $date = data_get($value, 'start_time');
                    $readable = $date ? Carbon::parse($date)->startOf('day')->format('Y-m-d') : '';

                    return [
                        'date'       => $readable,
                        'start_time' => data_get($value, 'start_time'),
                        'duration'   => data_get($value, 'duration'),
                        'title'      => data_get($value, 'name'),
                        'speakers'   => collect(data_get($value, 'speakerList', []))->map(function ($speaker) {
                            return [
                                'name'  => data_get($speaker, 'name'),
                                'image' => data_get($speaker, 'speaker_img'),
                            ];
                        }),
                    ];
                })->groupBy('date')->toArray();

                return [
                    'id'         => data_get($eventDetails, 'id'),
                    'name'       => data_get($eventDetails, 'name'),
                    'timezone'   => data_get($eventDetails, 'timezone'),
                    'status'     => data_get($eventDetails, 'status'),
                    'thumbnail'  => data_get($eventDetails, 'master_img_url'),
                    'start_time' => data_get($eventDetails, 'start_time'),
                    'end_time'   => data_get($eventDetails, 'end_time'),
                    'sessions'   => $sessionFormatted,
                ];
            }

            return false;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function fetchLabOrganizations($labIds)
    {
        try {
            $fetchLabOrganizations = Lab::whereIn('id', $labIds)->pluck('organization_id');

            return $fetchLabOrganizations;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function getAll()
    {
        try {
            return Lab::select();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
