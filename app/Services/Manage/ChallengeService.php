<?php

namespace App\Services\Manage;

use App\Helpers\FileUploadHelper;
use App\Helpers\UtilityHelper;
use App\Models\Challenge;
use App\Models\LabChallengeRedeem;
use App\Models\PitchTemplate;
use App\Services\Manage\MemberManagementService as ManageMemberManagementService;
use App\Services\Public\ChallengeSocialActivitiesService;
use App\Services\Public\MemberManagementService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\Log;

class ChallengeService
{
    public function getChallengeCountBasedOnOrganization($organizationId)
    {
        try {
            $challenge_count = Challenge::where(['organization_id' => $organizationId, 'is_pre_built' => '0', 'is_auto_created' => '0'])->count();

            return $challenge_count;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeList($request, $organization)
    {
        try {
            $challenge_list = Challenge::select()->where('organization_id', '=', $organization->id);

            $challenge_list = self::filterChallengeList($challenge_list, $request);

            return $challenge_list->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            return false;
        }
    }

    public static function filterChallengeList($challenge_list, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $challenge_list = $challenge_list->where('challenges.title', 'like', '%'.$request->search.'%');
            }

            if ($request->has('status') && !empty($request->status)) {
                $status = ($request->status == 'draft') ? '0' : (($request->status == 'published') ? '1' : (($request->status == 'deactivated') ? '2' : '3'));
                $challenge_list = $challenge_list->where('challenges.status', $status);
            } else {
                $challenge_list = $challenge_list->where('challenges.status', '1');
            }

            if ($request->filled('social_type') && in_array($request->social_type, ['liked', 'favourites'])) {
                $activityType = ($request->social_type == 'liked') ? 'like' : 'favourite';
                $challengeIds = ChallengeSocialActivitiesService::getChallengeBasedOnActivity($activityType)->pluck('challenge_id');
                $challenge_list->whereIn('challenges.id', $challengeIds);
            }

            if ($request->has('category') && !empty($request->category) && is_array($request->category)) {
                $challenge_list = $challenge_list->whereIn('challenges.category_id', $request->category);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch ($request->sort_by) {
                    case 'name-a-to-z':
                        $challenge_list = $challenge_list->orderBy('challenges.title', 'ASC');
                        break;
                    case 'name-z-to-a':
                        $challenge_list = $challenge_list->orderBy('challenges.title', 'DESC');
                        break;
                    case 'creation_date':
                        $challenge_list = $challenge_list->orderBy('challenges.created_at', 'ASC');
                        break;
                    default:
                        $challenge_list = $challenge_list->orderBy('challenges.id', 'ASC');
                }
            }

            if ($request->has('privacy') && !empty($request->privacy)) {
                switch ($request->privacy) {
                    case 'public':
                        $challenge_list = $challenge_list->where('challenges.privacy', '0');
                        break;
                    case 'private':
                        $challenge_list = $challenge_list->where('challenges.privacy', '1');
                        break;
                }
            }

            if ($request->has('skills') && !empty($request->skills) && is_array($request->skills)) {
                $challenge_list = $challenge_list->whereIn('challenges.id', function ($query) use ($request) {
                    $query->select('challenge_skills_groups_stacks.challenge_id')
                        ->from('challenge_skills_groups_stacks')
                        ->whereIn('challenge_skills_groups_stacks.foreign_id', $request->skills)
                        ->where('challenge_skills_groups_stacks.type', '0')
                        ->whereNull('challenge_skills_groups_stacks.deleted_at')
                        ->distinct();
                })->distinct('challenges.uuid');
            }
            if ($request->has('tags') && !empty($request->tags) && is_array($request->tags)) {
                $challenge_list = $challenge_list->whereIn('challenges.id', function ($query) use ($request) {
                    $query->select('challenge_tags_groups.challenge_id')
                        ->from('challenge_tags_groups')
                        ->whereIn('challenge_tags_groups.foreign_id', $request->tags)
                        ->where('challenge_tags_groups.type', '0')
                        ->whereNull('challenge_tags_groups.deleted_at')
                        ->distinct();
                })->distinct('challenges.uuid');
            }

            if ($request->has('request_status') && !empty($request->request_status)) {
                if (auth('api')->check()) {
                    $status_array = ['accepted', 'pending', 'declined'];
                    if (in_array($request->request_status, $status_array)) {
                        $challenge_list = $challenge_list->join('member_management', 'challenges.id', '=', 'member_management.module_id')
                            ->where(['member_management.module_type' => '2', 'member_management.email' => auth('api')->user()->email]);
                        switch ($request->request_status) {
                            case 'accepted':
                                $challenge_list->where('member_management.invite_status', '1');
                                break;
                            case 'pending':
                                $challenge_list->where('member_management.invite_status', '2');
                                break;
                            case 'declined':
                                $challenge_list->where('member_management.invite_status', '3');
                                break;
                            default:
                                $challenge_list;
                        }
                    }
                }
            }

            if ($request->has('duration_id') && $request->duration_id && is_array($request->duration_id)) {
                $challenge_list = $challenge_list->whereIn('duration_id', $request->duration_id);
            }

            if ($request->has('level_id') && $request->level_id && is_array($request->level_id)) {
                $challenge_list = $challenge_list->whereIn('level_id', $request->level_id);
            }

            return $challenge_list;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function uploadChallengeCoverImage($image)
    {
        try {
            $upload_challenge_cover_image = FileUploadHelper::uploadImageToS3($image, 'challenge');
            if ($upload_challenge_cover_image == false) {
                return false;
            }

            return $upload_challenge_cover_image;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function createChallenge($request, $upload_cover_image, $organizationId)
    {
        try {
            $status = config('constants.challenge_status.draft');
            if ($request->is_ai_created) {
                $status = config('constants.challenge_status.publish');
            } else {
                switch ($request->request_type) {
                    case 'draft':
                        $status = config('constants.challenge_status.draft');
                        break;
                    case 'publish':
                        $status = config('constants.challenge_status.publish');
                        break;
                    case 'archive':
                        $status = config('constants.challenge_status.archive');
                        break;
                    default:
                        $status = config('constants.challenge_status.draft');
                        break;
                }
            }

            $challenge_privacy = config('constants.challenge_privacy.no');
            switch ($request->privacy) {
                case 'yes':
                    $challenge_privacy = config('constants.challenge_privacy.yes');
                    break;
                case 'no':
                    $challenge_privacy = config('constants.challenge_privacy.no');
                    break;
                default:
                    $challenge_privacy = config('constants.challenge_privacy.no');
                    break;
            }

            $project_privacy = config('constants.challenge_privacy.no');
            switch ($request->project_privacy) {
                case 'yes':
                    $project_privacy = config('constants.challenge_privacy.yes');
                    break;
                case 'no':
                    $project_privacy = config('constants.challenge_privacy.no');
                    break;
                default:
                    $project_privacy = config('constants.challenge_privacy.no');
                    break;
            }

            $is_notification_enabled = config('constants.challenge_notification_enabled.no');
            switch ($request->is_notification_enabled) {
                case 'yes':
                    $is_notification_enabled = config('constants.challenge_notification_enabled.yes');
                    break;
                case 'no':
                    $is_notification_enabled = config('constants.challenge_notification_enabled.no');
                    break;
                default:
                    $is_notification_enabled = config('constants.challenge_notification_enabled.no');
                    break;
            }

            $is_open = config('constants.challenge_open_close.no');
            if ($request->is_ai_created) {
                $is_open = config('constants.challenge_open_close.yes');
            } else {
                switch ($request->is_open) {
                    case 'yes':
                        $is_open = config('constants.challenge_open_close.yes');
                        break;
                    case 'no':
                        $is_open = config('constants.challenge_open_close.no');
                        break;
                    default:
                        $is_open = config('constants.challenge_open_close.no');
                        break;
                }
            }

            $is_auto_created = config('constants.challenge_auto_created.no');
            switch ($request->is_auto_created) {
                case 'yes':
                    $is_auto_created = config('constants.challenge_auto_created.yes');
                    break;
                case 'no':
                    $is_auto_created = config('constants.challenge_auto_created.no');
                    break;
                default:
                    $is_auto_created = config('constants.challenge_auto_created.no');
                    break;
            }

            $is_ai_created = config('constants.challenge_ai_created.no');
            switch ($request->is_ai_created) {
                case 'yes':
                    $is_ai_created = config('constants.challenge_ai_created.yes');
                    break;
                case 'no':
                    $is_ai_created = config('constants.challenge_ai_created.no');
                    break;
                default:
                    $is_ai_created = config('constants.challenge_ai_created.no');
                    break;
            }

            $source_link = $request->source_link ?? null;
            if ($request->challengeTitle && $request->challengeDescription) {
                $request->title = $request->challengeTitle;
                $request->description = $request->challengeDescription;
            }

            $model = new Challenge();
            $slug = UtilityHelper::generateSlug($request->title, $model);

            $campusConnectStatus = $request->get('integrate_campus_connect', 'no');
            $challenge = new Challenge();
            $challenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $challenge->language = $request->language;
            $challenge->slug = $slug;
            $challenge->user_id = auth()->user()->id;
            $challenge->organization_id = $organizationId;
            $challenge->category_id = $request->category_id;
            $challenge->duration_id = $request->duration_id;
            $challenge->level_id = $request->level_id;
            $challenge->title = $request->title;
            $challenge->description = $request->description;
            $challenge->privacy = $challenge_privacy;
            $challenge->media_type = 'image';
            $challenge->media = $upload_cover_image;
            $challenge->status = $status;
            $challenge->source_link = $source_link;
            $challenge->agreement = ($request->has('agreement')) ? $request->agreement : 'No Terms and Conditions.';
            $challenge->is_notification_enabled = $is_notification_enabled;
            $challenge->project_privacy = $project_privacy;
            $challenge->is_open = $is_open;
            $challenge->is_auto_created = $is_auto_created;
            $challenge->is_ai_created = $is_ai_created;
            $challenge->campus_connect_status = config('constants.campus_connect_status.'.$campusConnectStatus);
            $challenge->save();

            return $challenge;
        } catch (Exception $e) {
            Log::error('Error in createChallenge in ChallengeService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function updateChallenge($slug, $request, $update_cover_image, $organizationId)
    {
        try {
            $challenge = Challenge::where('slug', $slug)->first();
            if ($challenge !== null) {
                $privacy = $challenge->privacy;
                if ($request->has('privacy')) {
                    switch ($request->privacy) {
                        case 'yes':
                            $privacy = config('constants.challenge_privacy.yes');
                            break;
                        case 'no':
                            $privacy = config('constants.challenge_privacy.no');
                            break;
                        default:
                            $privacy = config('constants.challenge_privacy.yes');
                            break;
                    }
                }

                $status = $challenge->status;
                if ($request->has('request_type')) {
                    $status = config('constants.challenge_status.draft');
                    switch ($request->request_type) {
                        case 'draft':
                            $status = config('constants.challenge_status.draft');
                            break;
                        case 'publish':
                            $status = config('constants.challenge_status.publish');
                            break;
                        case 'archive':
                            $status = config('constants.challenge_status.archive');
                            break;
                        default:
                            $status = config('constants.challenge_status.draft');
                            break;
                    }
                }

                $is_notification_enabled = $challenge->is_notification_enabled;
                if ($request->has('is_notification_enabled')) {
                    $is_notification_enabled = config('constants.challenge_notification_enabled.no');
                    switch ($request->is_notification_enabled) {
                        case 'yes':
                            $is_notification_enabled = config('constants.challenge_notification_enabled.yes');
                            break;
                        case 'no':
                            $is_notification_enabled = config('constants.challenge_notification_enabled.no');
                            break;
                        default:
                            $is_notification_enabled = config('constants.challenge_notification_enabled.yes');
                            break;
                    }
                }

                $project_privacy = $challenge->project_privacy;
                if ($request->has('project_privacy')) {
                    $project_privacy = config('constants.challenge_privacy.no');
                    switch ($request->project_privacy) {
                        case 'yes':
                            $project_privacy = config('constants.challenge_privacy.yes');
                            break;
                        case 'no':
                            $project_privacy = config('constants.challenge_privacy.no');
                            break;
                        default:
                            $project_privacy = config('constants.challenge_privacy.yes');
                            break;
                    }
                }
                $is_open = $challenge->is_open;
                if ($request->has('is_open')) {
                    $is_open = config('constants.challenge_open_close.no');
                    switch ($request->is_open) {
                        case 'yes':
                            $is_open = config('constants.challenge_open_close.yes');
                            break;
                        case 'no':
                            $is_open = config('constants.challenge_open_close.no');
                            break;
                        default:
                            $is_open = config('constants.challenge_open_close.yes');
                            break;
                    }
                }

                $is_auto_created = $challenge->is_auto_created;
                if ($request->has('is_auto_created')) {
                    $is_auto_created = config('constants.challenge_auto_created.no');
                    switch ($request->is_auto_created) {
                        case 'yes':
                            $is_auto_created = config('constants.challenge_auto_created.yes');
                            break;
                        case 'no':
                            $is_auto_created = config('constants.challenge_auto_created.no');
                            break;
                        default:
                            $is_auto_created = config('constants.challenge_auto_created.yes');
                            break;
                    }
                }
                $campusConnectStatus = $request->get('integrate_campus_connect', 'no');
                $challenge->language = ($request->has('language')) ? $request->language : $challenge->language;
                $challenge->organization_id = $organizationId;
                $challenge->category_id = ($request->has('category_id')) ? $request->category_id : $challenge->category_id;
                $challenge->duration_id = ($request->has('duration_id')) ? $request->duration_id : $challenge->duration_id;
                $challenge->level_id = ($request->has('level_id')) ? $request->level_id : $challenge->level_id;
                $challenge->title = ($request->has('title')) ? $request->title : $challenge->title;
                $challenge->description = ($request->has('description')) ? $request->description : $challenge->description;
                $challenge->privacy = $privacy;
                $challenge->media_type = 'image';
                $challenge->media = ($update_cover_image != null) ? $update_cover_image : $challenge->cover_image;
                $challenge->status = $status;
                $challenge->source_link = ($request->has('source_link') ? $request->source_link : $challenge->source_link);
                $challenge->agreement = ($request->has('agreement')) ? $request->agreement : $challenge->agreement;
                $challenge->is_notification_enabled = $is_notification_enabled;
                $challenge->project_privacy = $project_privacy;
                $challenge->is_open = $is_open;
                $challenge->is_auto_created = $is_auto_created;
                $challenge->campus_connect_status = config('constants.campus_connect_status.'.$campusConnectStatus);
                $challenge->save();

                return $challenge;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeBasedOnSlug($slug)
    {
        try {
            return Challenge::where(['slug' => $slug, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeBasedOnId($id)
    {
        try {
            return Challenge::where(['id' => $id, 'is_accessible' => '1'])->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkNameExistsOrNot($title)
    {
        try {
            $checkChallengeName = Challenge::where('title', $title)->first();
            if ($checkChallengeName) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteChallenge($challenge_id)
    {
        try {
            Challenge::find($challenge_id)->delete();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeBasedOnUUIDArray($challengeUUIDArray)
    {
        try {
            $challengeIds = Challenge::whereIn('uuid', $challengeUUIDArray)->pluck('id')->all();
            if ($challengeIds != null) {
                return $challengeIds;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeIdBasedOnId($id)
    {
        try {
            $challenge = Challenge::whereIn('id', $id)->pluck('id')->all();
            if ($challenge != null) {
                return $challenge;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeIdBasedOnUUID($challenge_uuid)
    {
        try {
            $challengeId = Challenge::where('uuid', $challenge_uuid)->value('id');

            return $challengeId ?: false;
        } catch (Exception $e) {
            Log::error('Error in getChallengeIdBasedOnUUID in ChallengeService.php: '.$e->getMessage());

            return false;
        }
    }

    public static function cloneChallenge($challengeId, $organization)
    {
        try {
            $originalChallenge = Challenge::find($challengeId);
            $model = new Challenge();
            $slug = UtilityHelper::generateSlug($organization->title.' '.$originalChallenge->title, $model);
            $clonedChallenge = $originalChallenge->replicate();
            $clonedChallenge->uuid = Randomize::chars(10)->alphanumeric()->unique()->generate();
            $clonedChallenge->title = $organization->title.' '.$originalChallenge->title;
            $clonedChallenge->slug = $slug;
            $clonedChallenge->user_id = auth()->user()->id;
            $clonedChallenge->organization_id = $organization->id;
            $clonedChallenge->save();

            return $clonedChallenge;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getChallengeListName($request, $organization)
    {
        try {
            $challenge_list = Challenge::select('uuid', 'title', 'media_type', 'media')->where(['organization_id' => $organization->id, 'is_accessible' => '1']);
            $challenge_list = self::filterChallengeList($challenge_list, $request);
            $limit = config('site-settings.listing_limit');

            return $challenge_list->limit($limit)->get();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function updatePreBuilt($challengeId, $is_pre_built)
    {
        try {
            $challengeUpdate = Challenge::find($challengeId);
            $challengeUpdate->is_pre_built = $is_pre_built;
            $challengeUpdate->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeBasedOnUUID($uuid)
    {
        try {
            return Challenge::where('uuid', $uuid)->first();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengeTemplateUpdatePreBuilt($challengeTemplateId)
    {
        try {
            $challengeTemplateData = LabChallengeRedeem::where(['challenge_template_id' => $challengeTemplateId, 'is_redeemed' => '0'])->first();
            if ($challengeTemplateData) {
                $challengeUpdate = Challenge::find($challengeTemplateData->challenge_id);
                if ($challengeUpdate) {
                    $challengeUpdate->is_pre_built = '0';
                    $challengeUpdate->save();
                    if ($challengeTemplateData->delete()) {
                        return true;
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function fetchChallengeDueDate($challengeData, $projectCreatedDate)
    {
        try {
            $challenge_timelines = null;
            if ($challengeData->challenge_timelines) {
                if ($challengeData->challenge_timelines->timeline_type == '0') {
                    switch ($challengeData->challenge_timelines->flexible_date_duration) {
                        case 'days':
                            $dateCount = $challengeData->challenge_timelines->flexible_date_number;
                            break;
                        case 'weeks':
                            $dateCount = $challengeData->challenge_timelines->flexible_date_number * 7;
                            break;
                        case 'months':
                            $dateCount = $challengeData->challenge_timelines->flexible_date_number * 30;
                            break;
                        default:
                            $dateCount = $challengeData->challenge_timelines->flexible_date_number;
                            break;
                    }
                    $durationDate = date_create(date('Y-m-d', strtotime($projectCreatedDate.' + '.$dateCount.'days')));
                    $formatDate = UtilityHelper::formatDateTime($durationDate);
                    $currentDate = UtilityHelper::formatDateTime(date_create(date('Y-m-d H:i:s')));
                    $dateResult = $formatDate < $currentDate;

                    switch ($challengeData->is_open) {
                        case '0':
                            $submission_status = 'submission';
                            if ($dateResult) {
                                $submission_status = 'late_submission';
                            }
                            $challenge_status = 'open';
                            break;
                        case '1':
                            $submission_status = 'late_submission';
                            $challenge_status = 'closed';
                            break;
                        case '2':
                            $submission_status = 'not_allowed';
                            $challenge_status = 'completed';
                            break;
                        default:
                    }

                    $challenge_timelines = [
                        'timeline_type'            => 'flexible',
                        'submission_deadline_date' => $formatDate,
                        'submission_status'        => $submission_status,
                        'challenge_status'         => $challenge_status,
                    ];
                } elseif ($challengeData->challenge_timelines->timeline_type == '1') {
                    switch ($challengeData->is_open) {
                        case '0':
                            $submission_status = 'submission';
                            $challenge_status = 'open';
                            break;
                        case '1':
                            $submission_status = 'late_submission';
                            $challenge_status = 'closed';
                            break;
                        case '2':
                            $submission_status = 'not_allowed';
                            $challenge_status = 'completed';
                            break;
                        default:
                    }

                    $challenge_timelines = [
                        'timeline_type'            => 'restricted',
                        'submission_deadline_date' => $challengeData->challenge_timelines->submission_deadline_date,
                        'submission_status'        => $submission_status,
                        'challenge_status'         => $challenge_status,
                    ];
                }
            }

            return $challenge_timelines;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeBasedOnSkillsAndTags($skills, $getUsersTags)
    {
        try {
            /*get challenge id Based on Skills*/
            $getChallengeIdBasedOnSkill = ChallengeSkillsGroupsStackService::getChallengeIdBasedOnSkills($skills);
            /*get challenge id based on tags*/
            $getChallengeIdBasedOnTags = ChallengeTagsGroupsService::getChallengeIdBasedOnSkills($getUsersTags);
            $challengeIds = $getChallengeIdBasedOnTags->merge($getChallengeIdBasedOnSkill)->unique();
            if (!empty($challengeIds)) {
                $challenges = Challenge::whereIn('id', $challengeIds)->where('user_id', '!=', auth()->user()->id)->pluck('id')->take(config('site-settings.explore_page_limit_max'));
            } else {
                $challenges = Challenge::where('user_id', '!=', auth()->user()->id)->pluck('id')->take(config('site-settings.explore_page_limit_min'));
            }
            if (count($challenges) < config('site-settings.explore_page_limit_max')) {
                $limit = config('site-settings.explore_page_limit_max') - count($challenges);
                $getNewChallengeIds = Challenge::where('user_id', '!=', auth()->user()->id)->whereNotIn('id', $challenges)->pluck('id')->take($limit);
                $challenges = $challenges->merge($getNewChallengeIds)->unique();
            }

            return Challenge::whereIn('id', $challenges)->take(config('site-settings.explore_page_limit_max'))->get();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getTrendingChallenge()
    {
        try {
            $getLatestChallengeIds = MemberManagementService::getLatestIdsBasedOnModule(config('constants.module_component_type.challenge'));
            $challenges = Challenge::select()->where('challenges.status', '1')->whereIn('id', $getLatestChallengeIds);

            return $challenges->get();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeBasedOnIds($id)
    {
        try {
            $challenge = Challenge::whereIn('id', $id)->get();
            if ($challenge != null) {
                return $challenge;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getChallengeDetailedBasedOnChallenges($challengeId, $created_at, $getProjectIdBasedTemplate = null)
    {
        try {
            $fetchChallenge = self::getChallengeBasedOnId($challengeId);
            if ($fetchChallenge) {
                $getTemplateId = ($getProjectIdBasedTemplate !== null) ? $getProjectIdBasedTemplate->template_id : ($fetchChallenge->challenge_project_template->template_id ?? 0);
                $getTemplate = self::getTemplate($getTemplateId);
                $projectDate = UtilityHelper::formatDateTime($created_at);
                $fetchChallengeDueDate = self::fetchChallengeDueDate($fetchChallenge, $projectDate);
                $challenge_details = [
                    'id'                => $fetchChallenge->id,
                    'uuid'              => $fetchChallenge->uuid,
                    'title'             => $fetchChallenge->title,
                    'slug'              => $fetchChallenge->slug,
                    'agreement'         => $fetchChallenge->agreement,
                    'is_accessible'     => ($fetchChallenge->is_accessible == '1') ? 'yes' : 'no',
                    'template_id'       => $getTemplate['template_id'],
                    'template_title'    => $getTemplate['template_title'],
                    'challenge_type'    => $fetchChallengeDueDate['timeline_type'],
                    'due_date'          => $fetchChallengeDueDate['submission_deadline_date'],
                    'submission_status' => $fetchChallengeDueDate['submission_status'],
                    'challenge_status'  => $fetchChallengeDueDate['challenge_status'],
                ];

                return $challenge_details;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function challengeUserInviteCount($organizationId)
    {
        try {
            $getChallengeAcceptedMembersBasedOnIds = [];
            $getChallengeBasedOnOrganization = Challenge::where(['organization_id' => $organizationId, 'is_auto_created' => '0'])->pluck('id');
            $getChallengeAcceptedMembersBasedOnIds = ManageMemberManagementService::getComponentAcceptedMembersBasedOnIds($getChallengeBasedOnOrganization, 'challenge');

            return $getChallengeAcceptedMembersBasedOnIds;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getTemplate($templateId)
    {
        try {
            $templateData = [];
            if ($templateId == '0') {
                $templateData = [
                    'template_id'    => $templateId,
                    'template_title' => __('responses.any_pitch_template'),
                ];
            } else {
                $template = PitchTemplate::where('id', $templateId)->first();
                if ($template) {
                    $templateData = [
                        'template_id'    => $template->id,
                        'template_title' => $template->title,
                    ];
                }
            }

            return $templateData;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteOrganizationChallenge($organizationId)
    {
        try {
            $fetchOrganizationChallenges = Challenge::where('organization_id', $organizationId)->pluck('id');
            if (!empty($fetchOrganizationChallenges)) {
                foreach ($fetchOrganizationChallenges as $organizationChallenge) {
                    $deleteOrganizationChallenge = self::deleteChallenge($organizationChallenge);
                    if (!$deleteOrganizationChallenge) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getChallengesBasedOnLevelId($levelId)
    {
        try {
            return Challenge::whereIn('level_id', $levelId)->pluck('id');
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getChallengeBasedOnOrganizationId($organizationId)
    {
        try {
            return Challenge::query()->where('organization_id', $organizationId)->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    public static function getChallengesBasedOnDuration($durationId)
    {
        try {
            return Challenge::whereIn('duration_id', $durationId)->pluck('id');
        } catch (\Exception $e) {
            return false;
        }
    }
    
    public static function getChallengeBasedOnUserId($userId)
    {
        try {
            return Challenge::query()->where('user_id', $userId)->get();
        } catch (Exception $exception) {
            Log::error($exception);

            return false;
        }
    }

    public static function getPaginatedChallengeBasedOnIds($ids)
    {
        try {
            return Challenge::whereIn('id', $ids)->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            return false;
        }
    }
}
