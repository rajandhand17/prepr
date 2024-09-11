<?php

namespace App\Services\Manage;

use App\Exceptions\InvitationQuotaExceededException;
use App\Helpers\MixpanelHelper;
use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;
use App\Notifications\ComponentJoinedNotification;
use App\Notifications\InviteMemberNotification;
use App\Services\LabHistoryService;
use App\Services\ModuleCompletionStatusService;
use App\Services\ProjectService;
use App\Services\UserService;
use DB;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use stdClass;

class MemberManagementService
{
    public static function getComponentBasedUsers($componentCollectionObject, $component, $request)
    {
        try {
            $module_type = null;
            $memberListCollection = MemberManagement::select();
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    $memberListCollection = $memberListCollection->where([
                        'module_id'   => $componentCollectionObject->id,
                        'module_type' => $module_type,
                    ]);
                    break;
                case 'lab':
                    $module_type = config('constants.member_management_component_type.lab');
                    $memberListCollection = $memberListCollection->where([
                        'module_id'   => $componentCollectionObject->id,
                        'module_type' => $module_type,
                    ]);
                    break;
                case 'lab-program':
                    $module_type = config('constants.member_management_component_type.lab_program');
                    $memberListCollection = $memberListCollection->where([
                        'module_id'   => $componentCollectionObject->id,
                        'module_type' => $module_type,
                    ]);
                    break;
                case 'challenge':
                    $module_type = config('constants.member_management_component_type.challenge');
                    $memberListCollection = $memberListCollection->where([
                        'module_id'   => $componentCollectionObject->id,
                        'module_type' => $module_type,
                    ]);
                    break;
                case 'challenge-path':
                    $componentCollectionObject->load('challenges');
                    $challengeIds = $componentCollectionObject->challenges?->pluck('challenge_id');
                    $module_type = config('constants.member_management_component_type.challenge');
                    $memberListCollection = $memberListCollection->where('module_type', $module_type)->whereIn('module_id', $challengeIds);
                    break;
                default:
                    $module_type = null;
                    $memberListCollection = null;
                    break;
            }
            if ($module_type != null) {
                $memberList = self::filterUserList($memberListCollection, $request);

                return $memberList->paginate(config('site-settings.pagination_per_page'));
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function filterUserList($componentCollectionObject, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $componentCollectionObject = $componentCollectionObject->where(function ($query) use ($request) {
                    $query->where('invitee_name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            }
            if ($request->has('role') && !empty($request->role)) {
                $componentCollectionObject = $componentCollectionObject->where('role', $request->role);
            }

            if (isset($request->request_status) && isset($request->invite_status)) {
                $requestAndInviteStatus = $request->request_status.','.$request->invite_status;
                $statusIds = self::getInviteAndRequestStatus($requestAndInviteStatus, 'combine');
                if ($statusIds != null) {
                    $componentCollectionObject = $componentCollectionObject->whereIn('invite_status', $statusIds);
                }
            } elseif (isset($request->request_status)) {
                $request_status = self::getInviteAndRequestStatus($request->request_status, 'single');
                if ($request_status != null) {
                    $componentCollectionObject = $componentCollectionObject->where('invite_status', $request_status);
                }
            } elseif (isset($request->invite_status)) {
                $invite_status = self::getInviteAndRequestStatus($request->invite_status, 'single');
                if ($invite_status != null) {
                    $componentCollectionObject = $componentCollectionObject->where('invite_status', $invite_status);
                }
            }

            if ($request->has('invite_type') && !empty($request->invite_type)) {
                $invite_type = null;
                switch ($request->invite_type) {
                    case 'email':
                        $invite_type = config('constants.member_management_invite_type.email');
                        break;
                    case 'network':
                        $invite_type = config('constants.member_management_invite_type.network');
                        break;
                    case 'join_request':
                        $invite_type = config('constants.member_management_invite_type.join_request');
                        break;
                    case 'csv':
                        $invite_type = config('constants.member_management_invite_type.csv');
                        break;
                    case 'auto_created':
                        $invite_type = 'auto_created';
                        break;
                    default:
                        $invite_type = null;
                }
                if ($invite_type != null) {
                    if ($invite_type == 'auto_created') {
                        $componentCollectionObject = $componentCollectionObject->where('type', config('constants.member_management_type.auto_created'));
                    } else {
                        $componentCollectionObject = $componentCollectionObject->where('invite_type', $invite_type);
                    }
                }
            }
            if ($request->has('email_status') && !empty($request->email_status)) {
                $email_status = null;
                switch ($request->email_status) {
                    case 'scheduled':
                        $email_status = config('constants.member_management_email_status.scheduled');
                        break;
                    case 'sent':
                        $email_status = config('constants.member_management_email_status.sent');
                        break;
                    case 'fail':
                        $email_status = config('constants.member_management_email_status.fail');
                        break;
                    case 'NA':
                        $email_status = config('constants.member_management_email_status.na');
                        break;
                    default:
                        $email_status = null;
                }
                if ($email_status != null) {
                    $componentCollectionObject = $componentCollectionObject->where('email_status', $email_status);
                }
            }

            if ($request->has('project_status') && !empty($request->project_status)) {
                // Define the allowed statuses
                $allowedStatuses = ['not_submitted', 'submitted', 'late_submitted'];

                if (in_array($request->project_status, $allowedStatuses)) {
                    // Extract module IDs and emails from the collection
                    $moduleIds = $componentCollectionObject->pluck('module_id');
                    $emails = $componentCollectionObject->pluck('email');

                    // Get user IDs based on the emails
                    $userIds = UserService::getUserIdsByEmail($emails);

                    // Retrieve the appropriate status from the config based on the project status
                    $status = config('constants.project_is_submitted.'.$request->project_status);

                    // Get projects filtered by challenge status and user IDs
                    $projects = ProjectService::checkUserChallengeStatusFilterByStatus($moduleIds->toArray(), $userIds->toArray(), $status);
                    if (!empty($projects)) {
                        // Extract challenge IDs and user emails from the filtered projects
                        $challengeIds = $projects->pluck('challenge_id');
                        $userEmails = UserService::getUsersByIds($projects->pluck('user_id'))->pluck('email');

                        // Filter the original collection based on the challenge IDs and user emails
                        $componentCollectionObject = $componentCollectionObject->whereIn('module_id', $challengeIds)
                            ->whereIn('email', $userEmails);
                    } else {
                        // If no matching projects, clear the collection
                        $componentCollectionObject = collect();
                    }
                } else {
                    $componentCollectionObject = collect();
                }
            }

            if (isset($request->progress_status)) {
                switch ($request->component) {
                    case 'lab':
                        $component = '0';
                        break;
                    case 'lab-program':
                        $component = '1';
                        break;
                }
                switch ($request->progress_status) {
                    case 'not_started':
                        $module_status = '0';
                        break;
                    case 'in_progress':
                        $module_status = '1';
                        break;
                    case 'completed':
                        $module_status = '2';
                        break;
                }
                $moduleIds = $componentCollectionObject->pluck('module_id');
                $emailIds = $componentCollectionObject->pluck('email');
                $fetchUserIds = UserService::getUserIdsByEmail($emailIds);
                $getUserIds = ModuleCompletionStatusService::fetchComponentProgressBasedOnIds($moduleIds, $component, $module_status, $fetchUserIds);
                $userEmails = UserService::getUserEmailsById($getUserIds->pluck('user_id'));
                $componentCollectionObject = $componentCollectionObject->whereIn('email', $userEmails);
            }

            return $componentCollectionObject;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getInviteAndRequestStatus($inviteAndRequestStatus, $statusType)
    {
        try {
            $statusData = null;
            if ($statusType == 'single') {
                switch ($inviteAndRequestStatus) {
                    case 'invited':
                        $statusData = config('constants.member_management_invite_status.invited');
                        break;
                    case 'accepted':
                        $statusData = config('constants.member_management_invite_status.accepted');
                        break;
                    case 'pending':
                        $statusData = config('constants.member_management_invite_status.pending');
                        break;
                    case 'declined':
                        $statusData = config('constants.member_management_invite_status.declined');
                        break;
                    case 'auto_created':
                        $statusData = config('constants.member_management_invite_status.auto_created');
                        break;
                }

                return $statusData;
            } elseif ($statusType == 'combine') {
                $statusData = [];
                foreach (explode(',', $inviteAndRequestStatus) as $statusIdData) {
                    switch ($statusIdData) {
                        case 'invited':
                            $statusId = config('constants.member_management_invite_status.invited');
                            $statusData[] = $statusId;
                            break;
                        case 'accepted':
                            $statusId = config('constants.member_management_invite_status.accepted');
                            $statusData[] = $statusId;
                            break;
                        case 'pending':
                            $statusId = config('constants.member_management_invite_status.pending');
                            $statusData[] = $statusId;
                            break;
                        case 'declined':
                            $statusId = config('constants.member_management_invite_status.declined');
                            $statusData[] = $statusId;
                            break;
                        case 'auto_created':
                            $statusId = config('constants.member_management_invite_status.auto_created');
                            $statusData[] = $statusId;
                            break;
                    }
                }

                return $statusData;
            }
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getTemplate($request, $component)
    {
        try {
            $module_type = null;
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.email_template_module_type.organization');
                    break;
                case 'lab':
                    $module_type = config('constants.email_template_module_type.lab');
                    break;
                case 'challenge':
                    $module_type = config('constants.email_template_module_type.challenge');
                    break;
                case 'challenge-path':
                    $module_type = config('constants.email_template_module_type.challenge');
                    break;
                case 'project':
                    $module_type = config('constants.email_template_module_type.project');
                    break;
                case 'lab-program':
                    $module_type = config('constants.email_template_module_type.lab_program');
                    break;
                default:
                    $module_type = null;
                    break;
            }

            return EmailTemplateService::getEmailTemplate(config('constants.email_template_type.invitation'), $module_type, $request->language);
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRecordsFromCsv($request)
    {
        try {
            $memberList = [];
            $inviteStatus = null;

            if ($request->auto_invite === 'yes') {
                $inviteStatus = config('constants.member_management_invite_status.accepted');
            }

            if ($request->hasFile('invite_email')) {
                if (($handle = fopen($request->invite_email, 'r')) !== false) {
                    $header = fgetcsv($handle, 0, ',');
                    $count_header = count($header);
                    /**Checking columns names in csv  */
                    if ($count_header == 2 && in_array('Name', $header) && in_array('Email', $header)) {
                        /**checking place of email column one or two */
                        if ($header[0] == 'Email') {
                            $email_column = 0;
                            $name_column = 1;
                        } else {
                            $email_column = 1;
                            $name_column = 0;
                        }
                    } else {
                        return false;
                    }
                    /**getting data from csv and convert in array */
                    while (($csv_get_data = fgetcsv($handle, 1000, ',')) !== false) {
                        $member = [
                            'type'          => config('constants.member_management_type.invite'),
                            'invite_type'   => config('constants.member_management_invite_type.csv'),
                            'invitee_name'  => $csv_get_data[$name_column],
                            'invitee_email' => $csv_get_data[$email_column],
                        ];

                        if (!empty($inviteStatus)) {
                            $member['invite_status'] = $inviteStatus;
                        }

                        $memberList[] = $member;
                    }
                    fclose($handle);
                    if (!empty($memberList)) {
                        return $memberList;
                    }

                    return false;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRecordsFromEmailArray($request)
    {
        try {
            $memberList = [];
            $inviteStatus = null;
            $type = config('constants.member_management_type.'.$request->type);

            if ($request->auto_invite === 'yes') {
                $inviteStatus = config('constants.member_management_invite_status.accepted');
            }

            if (is_array($request->invite_email)) {
                foreach ($request->invite_email as $email) {
                    $user = UserService::getUserByEmail($email);
                    $name = null;
                    if ($user) {
                        $name = $user->first_name.' '.$user->last_name;
                    }
                    $member = [
                        'type'          => $type,
                        'invite_type'   => config('constants.member_management_invite_type.email'),
                        'invitee_name'  => $name,
                        'invitee_email' => $email,
                    ];

                    if (!empty($inviteStatus)) {
                        $member['invite_status'] = $inviteStatus;
                    }

                    $memberList[] = $member;
                }

                if (!empty($memberList)) {
                    return $memberList;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getRecordsFromJoinRequest()
    {
        try {
            $memberList = [];
            if (auth()->user()) {
                $memberList[] = [
                    'type'          => config('constants.member_management_type.join_request'),
                    'invite_type'   => config('constants.member_management_invite_type.join_request'),
                    'invitee_name'  => auth()->user()->full_name,
                    'invitee_email' => auth()->user()->email,
                    'invite_status' => config('constants.member_management_invite_status.pending'),
                ];
                if (!empty($memberList)) {
                    return $memberList;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function addMembers($componentCollectionObject, $component, $request, $memberList, $totalInvitationSent = 0)
    {
        try {
            $already_members = [];
            $invalid_emails = [];
            $invited_emails = [];
            switch ($component) {
                case 'organization':
                    $module_name = 'Organization';
                    $module_type = config('constants.member_management_component_type.organization');
                    $addedMemberResponse = __('responses.create_member_manger_success_organization');
                    break;
                case 'lab':
                    $module_name = 'Lab';
                    $module_type = config('constants.member_management_component_type.lab');
                    $addedMemberResponse = __('responses.create_member_manger_success_lab');
                    break;
                case 'challenge':
                    $module_name = 'Challenge';
                    $module_type = config('constants.member_management_component_type.challenge');
                    $addedMemberResponse = __('responses.create_member_manger_success_challenge');
                    break;
                case 'lab-program':
                    $module_name = 'Lab Program';
                    $module_type = config('constants.member_management_component_type.lab_program');
                    $addedMemberResponse = __('responses.create_member_manger_success_lab_program');
                    break;
            }
            $auto_invite = config('constants.member_management_auto_invite.no');
            switch ($request->auto_invite) {
                case 'yes':
                    $auto_invite = config('constants.member_management_auto_invite.yes');
                    break;
                case 'no':
                    $auto_invite = config('constants.member_management_auto_invite.no');
                    break;
                case 'NA' :
                    $auto_invite = config('constants.member_management_auto_invite.na');
                    break;
                default:
                    $auto_invite = config('constants.member_management_auto_invite.no');
            }
            $email_status = config('constants.member_management_auto_invite.scheduled');
            switch ($request->email_status) {
                case 'scheduled':
                    $email_status = config('constants.member_management_email_status.scheduled');
                    break;
                case 'sent':
                    $email_status = config('constants.member_management_email_status.sent');
                    break;
                case 'failed':
                    $email_status = config('constants.member_management_email_status.fail');
                    break;
                case 'NA':
                    $email_status = config('constants.member_management_email_status.na');
                    break;
                default:
                    $email_status = config('constants.member_management_email_status.scheduled');
            }
            if ($module_type !== null) {
                DB::beginTransaction();
                foreach ($memberList as $member) {
                    if (UtilityHelper::validEmail($member['invitee_email'])) {
                        $checkMemberExists = MemberManagement::where([
                            'module_id'   => $componentCollectionObject->id,
                            'module_type' => $module_type,
                            'email'       => $member['invitee_email'],
                        ])->first();
                        if ($checkMemberExists == null) {
                            //check user email limit here
                            $userData = auth()->user();
                            $organization = UtilityHelper::UserIdBasedPreferredOrganization($userData);
                            $organization->load('chargebee_details');
                            $userInviteLimit = $organization->chargebee_details->user_invite_limits;

                            $isWithinInviteLimit = $userInviteLimit == -1 || $userInviteLimit > $totalInvitationSent;
                            if (!$isWithinInviteLimit) {
                                throw new InvitationQuotaExceededException(__('responses.reached_invitation_limit'));
                            }

                            $totalInvitationSent++;

                            $invite_status = config('constants.member_management_invite_status.invited');
                            if ($auto_invite == 0) {
                                $invite_status = config('constants.member_management_invite_status.pending');
                            }
                            if ($auto_invite == 1) {
                                $invite_status = config('constants.member_management_invite_status.accepted');
                            }
                            if (isset($member['invite_status'])) {
                                $invite_status = $member['invite_status'];
                            } else {
                                $invite_status = $member['type'];
                            }
                            if ($auto_invite == 2) {
                                if ($member['type'] == '1') {
                                    $invite_status = config('constants.member_management_invite_status.pending');
                                } elseif ($member['type'] == '2') {
                                    $invite_status = config('constants.member_management_invite_status.auto_created');
                                }
                            }

                            $subject = $request->subject_line;
                            $emailBody = $request->email_body;
                            $user_name = UserService::joinName(auth()->user()->first_name ?? '', auth()->user()->last_name ?? '');

                            if ($emailBody) {
                                $emailBody = str_replace('user_name', $user_name, str_replace('component_title', $componentCollectionObject->title, $emailBody));
                            }

                            if (empty($request->subject_line) || empty($request->email_body)) {
                                $getTemplate = EmailTemplateService::getEmailTemplate(config('constants.email_template_type.invitation'), $module_type, $request->language);

                                if ($getTemplate) {
                                    //replace component title and user name with actual data
                                    $getTemplate->body_content = str_replace('user_name', $user_name, str_replace('component_title', $componentCollectionObject->title, $getTemplate->body_content));

                                    if (empty($request->subject_line)) {
                                        $subject = $getTemplate->subject;
                                    }
                                    if (empty($request->email_body)) {
                                        $emailBody = $getTemplate->body_content;
                                    }
                                }
                            }
                            $invitedMember = MemberManagement::create([
                                'uuid'          => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                                'type'          => $member['type'],
                                'invite_type'   => $member['invite_type'],
                                'module_id'     => $componentCollectionObject->id,
                                'module_type'   => $module_type,
                                'inviter_id'    => ($member['type'] == 0 && auth()->user()) ? auth()->user()->id : $componentCollectionObject->user_id,
                                'role'          => $member['role'] ?? $request->role,
                                'email'         => $member['invitee_email'],
                                'auto_invite'   => $auto_invite,
                                'invite_status' => $invite_status,
                                'invitee_name'  => $member['invitee_name'],
                                'email_status'  => $email_status,
                                'subject_line'  => $subject,
                                'email_body'    => $emailBody,
                            ]);

                            if ($component == 'lab' && $member['type'] == '1' && $module_type == '1') {
                                $userId = auth()->user()->id;
                                $activity = auth()->user()->full_name.' '.__('responses.lab_joined_activity').' '.$componentCollectionObject->title;
                                $labHistoryService = new LabHistoryService();
                                $labHistoryService->storeHistory($componentCollectionObject->id, $userId, $activity);
                            }

                            MixpanelHelper::mixpanel_tracking(config('mixpanel.send_invite'), $invitedMember->id);
                            $invitee_name = $member['invitee_name'] != null ? $member['invitee_name'] : 'Solver';
                            $email_detail = ['invitee_email' => $member['invitee_email'], 'invitee_name' => $invitee_name, 'subject' => $subject, 'body' => $emailBody, 'slug' => config('site-settings.frontend_site_url'), 'component' => $component, 'inviter_name' =>  auth()->user()->full_name, 'comp_title' =>  $componentCollectionObject->title, 'comp_image' => $componentCollectionObject->media, 'module_name' => $module_name, 'role' => $member['role'] ?? $request->role, 'comp_mediaType'=> $componentCollectionObject->media_type, 'org_image' => $componentCollectionObject->cover_image];
                            if ($member['invite_type'] === 'join_request') {
                                $user = UserService::getUserById($componentCollectionObject->user_id);
                                $user->notify(new ComponentJoinedNotification(__('responses.noti_new_user_request'), __('responses.noti_new_user_request_message').$component.'.'));
                            }
                            Notification::route('mail', $member['invitee_email'])->notify(new InviteMemberNotification($email_detail));
                            $invited_emails[] = $member['invitee_email'];
                        } else {
                            if ($checkMemberExists['invite_status'] == '3' || $checkMemberExists['invite_status'] == '2') {
                                $subject = $request->subject_line;
                                $emailBody = $request->email_body;
                                $user_name = UserService::joinName(auth()->user()->first_name, auth()->user()->last_name);
                                if ($emailBody) {
                                    $emailBody = str_replace('user_name', $user_name, str_replace('component_title', $componentCollectionObject->title, $emailBody));
                                }
                                if (empty($request->subject_line) || empty($request->email_body)) {
                                    $getTemplate = EmailTemplateService::getEmailTemplate(config('constants.email_template_type.invitation'), $module_type, $request->language);
                                    if ($getTemplate) {
                                        //replace component title and user name with actual data
                                        $getTemplate->body_content = str_replace('user_name', $user_name, str_replace('component_title', $componentCollectionObject->title, $getTemplate->body_content));

                                        if (empty($request->subject_line)) {
                                            $subject = $getTemplate->subject;
                                        }
                                        if (empty($request->email_body)) {
                                            $emailBody = $getTemplate->body_content;
                                        }
                                    }
                                }
                                $emailResendCount = $checkMemberExists->email_resend_count + 1;
                                MemberManagement::where('id', $checkMemberExists['id'])
                                    ->update([
                                        'invite_status'      => config('constants.member_management_invite_status.invited'),
                                        'email_status'       => '0',
                                        'email_resend_count' => $emailResendCount,
                                    ]);
                                $invitee_name = $member['invitee_name'] != null ? $member['invitee_name'] : 'Solver';
                                $email_detail = ['invitee_email' => $member['invitee_email'], 'invitee_name' => $invitee_name, 'subject' => $subject, 'body' => $emailBody, 'slug' => config('site-settings.frontend_site_url'), 'component' => $component, 'inviter_name' =>  auth()->user()->full_name, 'comp_title' =>  $componentCollectionObject->title, 'comp_image' => $componentCollectionObject->media, 'module_name' => $module_name, 'role' => $member['role'] ?? $request->role, 'comp_mediaType'=> $componentCollectionObject->media_type, 'org_image' => $componentCollectionObject->cover_image];
                                Notification::route('mail', $member['invitee_email'])->notify(new InviteMemberNotification($email_detail));
                                $invited_emails[] = $member['invitee_email'];
                            } else {
                                $already_members[] = $member['invitee_email'];
                            }
                        }
                    } else {
                        $invalid_emails[] = $member['invitee_email'];
                    }
                }
                DB::commit();
                if (count($invalid_emails) > 0 || count($already_members) > 0) {
                    if (count($invited_emails) < 1) {
                        switch ($component) {
                            case 'organization':
                                $addedMemberResponse = __('responses.create_member_manger_error_org');
                                break;
                            case 'lab':
                                $addedMemberResponse = __('responses.create_member_manger_error_lab');
                                break;
                            case 'challenge':
                                $addedMemberResponse = __('responses.create_member_manger_error_cha');
                                break;
                            case 'lab-program':
                                $addedMemberResponse = __('responses.create_member_manger_error_lab_program');
                                break;
                            default:
                                $addedMemberResponse = __('responses.create_member_manger_error');
                                break;
                        }
                    } else {
                        $addedMemberResponse = __('responses.create_member_manger_error_certain');
                    }
                }
                $data = [
                    'invalid_emails'      => $invalid_emails,
                    'invited_emails'      => $invited_emails,
                    'already_members'     => $already_members,
                    'add_member_response' => $addedMemberResponse,
                ];

                return $data;
            }
            DB::rollBack();

            return false;
        } catch (InvitationQuotaExceededException $e) {
            throw $e;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public function checkJoinedOrNot($checkComponentBasedOnSlug, $moduleType)
    {
        try {
            $records = MemberManagement::where('email', auth()->user()->email)->where(['module_id' => $checkComponentBasedOnSlug->id, 'module_type' => $moduleType])->first();
            if ($records) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteMembers($checkComponentBasedOnSlug, $component, $request)
    {
        try {
            $module_type = self::getModuleType($component);
            $member = MemberManagement::whereIn('email', $request->email)->where(['module_id'=>$checkComponentBasedOnSlug->id, 'module_type'=>$module_type])->first();
            if ($member) {
                $member_manger = MemberManagement::whereIn('email', $request->email)->where(['module_id'=>$checkComponentBasedOnSlug->id, 'module_type'=>$module_type])->delete();
                if ($module_type == '1') {
                    $lab = LabService::getLabBasedOnId($member->module_id);
                    $request = \Illuminate\Http\Request::capture();
                    $request->organization_id = $lab->organization_id;
                    $request->privacy = $lab->privacy;
                    $request->title = $lab->title;
                    $request->category = $lab->category_id;

                    if ($component == 'lab') {
                        $userId = auth()->user()->id;
                        $activity = auth()->user()->full_name.' '.__('responses.lab_un_joined_activity').' '.$lab->title;
                        $labHistoryService = new LabHistoryService();
                        $labHistoryService->storeHistory($lab->id, $userId, $activity);
                    }

                    MixpanelHelper::mixpanel_tracking(config('mixpanel.leave_lab'), $request, auth()->user(), $request->ip());
                }

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteAllMembers($checkComponentBasedOnSlug, $component, $request)
    {
        try {
            $module_type = self::getModuleType($component);
            if (isset($module_type)) {
                MemberManagement::where(['module_id' => $checkComponentBasedOnSlug->id, 'module_type' => $module_type])->delete();
                MixpanelHelper::mixpanel_tracking(config('mixpanel.leave_lab'), $request, auth()->user(), $request->ip());

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function approveAllPendingJoinRequests($checkComponentBasedOnSlug, $component, $request)
    {
        try {
            $module_type = self::getModuleType($component);
            if (isset($module_type)) {
                MemberManagement::where(['module_id' => $checkComponentBasedOnSlug->id, 'module_type' => $module_type, 'type' => '1', 'invite_type' => '2'])->update(['invite_status' => '1']);
                MixpanelHelper::mixpanel_tracking(config('mixpanel.leave_lab'), $request, auth()->user(), $request->ip());

                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getModuleType($component)
    {
        try {
            $module_type = null;
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    break;
                case 'lab':
                    $module_type = config('constants.member_management_component_type.lab');
                    break;
                case 'challenge':
                    $module_type = config('constants.member_management_component_type.challenge');
                    break;
                case 'lab-program':
                    $module_type = config('constants.member_management_component_type.lab_program');
                    break;
            }

            return $module_type;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component)
    {
        try {
            $module_type = self::getModuleType($component);
            $member_manger = MemberManagement::whereIn('email', $request->email)->where(['module_id' => $checkComponentBasedOnSlug->id, 'module_type' => $module_type, 'invite_status' => '2'])->get();
            if ($member_manger->isNotEmpty()) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action)
    {
        try {
            $module_type = self::getModuleType($component);
            switch ($action) {
                case 'accept':
                    $invite_status = config('constants.member_management_invite_status.accepted');
                    break;
                case 'decline':
                    $invite_status = config('constants.member_management_invite_status.declined');
                    break;
            }
            $member_manger = MemberManagement::whereIn('email', $request->email)->where(['module_id' => $checkComponentBasedOnSlug->id, 'module_type' => $module_type, 'invite_status' => '2'])->get();
            foreach ($member_manger as $member) {
                $member->invite_status = $invite_status;
                $member->inviter_id = auth()->user()->id;
                $member->save();
                if ($invite_status == '1' && $component == 'lab') {
                    $lab = LabService::getLabBasedOnId($member->module_id);
                    $request->organization_id = $lab->organization_id;
                    $request->privacy = $lab->privacy;
                    $request->title = $lab->title;
                    $request->category = $lab->category_id;
                    MixpanelHelper::mixpanel_tracking(config('mixpanel.join_lab'), $request, auth()->user(), $request->ip());
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function changeRoleByUuid($request, $component)
    {
        try {
            DB::beginTransaction();
            $checkMember = MemberManagement::where('uuid', $request->id)->first();
            if ($checkMember != null) {
                if ($component == 'organization') {
                    $getOrganization = OrganizationService::getOrganizationExistBasedOnId($checkMember->module_id);
                    if ($checkMember->invite_status == config('constants.member_management_invite_status.accepted')) {
                        $getUser = UserService::getUserByEmail($checkMember->email);
                        $getOldRole = RolesService::getRoleBasedOnDisplayName($checkMember->role);
                        $getNewRole = RolesService::getRoleBasedOnDisplayName($request->role);
                        if ($getUser && $getOldRole && $getNewRole) {
                            $getUser->detachRole($getOldRole, $getOrganization);
                            $getUser->attachRoles($getNewRole, $getOrganization);
                        }
                    }
                }
                $checkMember->role = $request->role;
                $checkMember->save();
                DB::commit();

                return true;
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            DB::rollBack();

            return false;
        }
    }

    public static function getFilteredMemberManagementList($filterData)
    {
        try {
            return MemberManagement::select(
                'id',
                'type',
                'invite_type',
                'module_id',
                'module_type',
                'inviter_id',
                'role',
                'invite_status',
                'email',
                'auto_invite',
                'invitee_name',
                'email_status'
            )->where($filterData)->get();
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function setJoinRequestParameters($language)
    {
        try {
            $requestedData = new stdClass();
            $requestedData->type = 'join_request';
            $requestedData->invite_type = 'join_request';
            $requestedData->auto_invite = 'NA';
            $requestedData->role = null;
            $requestedData->subject_line = null;
            $requestedData->email_body = null;
            $requestedData->language = $language;
            $requestedData->email_status = 'NA';

            return $requestedData;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function isUserBelongToPrepr($user = null)
    {
        try {
            $user = $user ?? auth()->user();
            $invitedUser = MemberManagement::query()
                ->where('module_id', config('go1.go1_prepr_id'))
                ->where('module_type', config('constants.member_management_component_type.organization'))
                ->where('email', $user->email)
                ->first();

            if (!$invitedUser) {
                return false;
            }

            return true;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function canPlayGO1Resoruces($user = null)
    {
        try {
            $user = $user ?? auth()->user();

            return $this->isUserBelongToPrepr($user);
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public function canCreateGO1Resource($user = null)
    {
        try {
            $user = $user ?? auth()->user();

            $isPreprUser = $this->isUserBelongToPrepr($user);
            if ($user && $user->hasPermission('create_resource_module_from_go1') && $isPreprUser) {
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getMembersManagerUsersBasedOnFilter($request)
    {
        try {
            $membersEmails = new Collection();
            $organization_id = new Collection();
            $lab_id = new Collection();
            $challenge_id = new Collection();
            if ($request->has('organization_id') && !empty($request->organization_id)) {
                $organization_id = MemberManagementService::getMembersBasedOnComponentId('organization', $request->organization_id);
            }
            if ($request->has('lab_id') && !empty($request->lab_id)) {
                $lab_id = MemberManagementService::getMembersBasedOnComponentId('lab', $request->lab_id);
            }
            if ($request->has('challenge_id') && !empty($request->challenge_id)) {
                $challenge_id = MemberManagementService::getMembersBasedOnComponentId('challenge', $request->challenge_id);
            }

            $mergedEmails = $membersEmails->merge($organization_id)->merge($lab_id)->merge($challenge_id);

            return $mergedEmails;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getMembersBasedOnComponentId($component, $componentId)
    {
        try {
            switch ($component) {
                case 'lab':
                    $memberManagement = self::getFilteredMemberManagementList(
                        [
                            'module_id'     => $componentId,
                            'module_type'   => config('constants.module_component_type.lab'),
                            'invite_status' => config('constants.member_management_invite_status.accepted'),
                        ]
                    )->pluck('email');
                    break;
                case 'organization':
                    $memberManagement = self::getFilteredMemberManagementList(
                        [
                            'module_type'   => config('constants.module_component_type.organization'),
                            'module_id'     => $componentId,
                            'invite_status' => config('constants.member_management_invite_status.accepted'),
                        ]
                    )->pluck('email');
                    break;
                case 'challenge':
                    $memberManagement = self::getFilteredMemberManagementList(
                        [
                            'module_type'   => config('constants.module_component_type.challenge'),
                            'module_id'     => $componentId,
                            'invite_status' => config('constants.member_management_invite_status.accepted'),
                        ]
                    )->pluck('email');
                    break;
            }

            return $memberManagement;
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function getMemberManagerModuleEmail($id)
    {
        try {
            return MemberManagement::where(['user_id' => $id])->where(function ($query) {
                $query->where('role', '=', 'challenge_manager')
                    ->orWhere('role', '=', 'lab_manager')
                    ->orWhere('role', '=', 'resource_manager')
                    ->orWhere('role', '=', 'organization_manager')
                    ->orWhere('role', '=', 'super_admin');
            })->pluck('email');
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getOrganizationIdBasedOnInviterId($component, $inviterId)
    {
        try {
            $memberManagement = '';
            switch ($component) {
                case 'lab':
                    $memberManagement = MemberManagement::join('labs', 'member_management.module_id', '=', 'labs.id')
                        ->where([
                            'inviter_id'    => $inviterId,
                            'module_type'   => config('constants.module_component_type.lab'),
                            'invite_status' => config('constants.member_management_invite_status.accepted'),
                        ])->pluck('organization_id')->unique();
                    break;
                case 'challenge':

                    $memberManagement = MemberManagement::join('challenges', 'member_management.module_id', '=', 'challenges.id')
                        ->where([
                            'inviter_id'    => $inviterId,
                            'module_type'   => config('constants.module_component_type.challenge'),
                            'invite_status' => config('constants.member_management_invite_status.accepted'),
                        ])->pluck('organization_id')->unique();
                    break;
            }

            return $memberManagement;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getComponentAcceptedMembersBasedOnIds($moduleIds, $component)
    {
        try {
            switch ($component) {
                case 'organization':
                    $memberManagement = MemberManagement::whereIn('module_id', $moduleIds)->where('role', 'User')->where([
                        'module_type'   => config('constants.module_component_type.organization'),
                        'invite_status' => config('constants.member_management_invite_status.accepted'),
                    ])->pluck('email');
                    break;
                case 'lab':
                    $memberManagement = MemberManagement::whereIn('module_id', $moduleIds)->where([
                        'module_type'   => config('constants.module_component_type.lab'),
                        'invite_status' => config('constants.member_management_invite_status.accepted'),
                    ])->pluck('email');
                    break;
                case 'challenge':
                    $memberManagement = MemberManagement::whereIn('module_id', $moduleIds)->where([
                        'module_type'   => config('constants.module_component_type.challenge'),
                        'invite_status' => config('constants.member_management_invite_status.accepted'),
                    ])->pluck('email');
                    break;
            }

            return $memberManagement;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getComponentAcceptedManagerMembersBasedOnIds($organizationId)
    {
        try {
            $memberManagement = MemberManagement::where('role', '!=', 'User')->where([
                'module_id'     => $organizationId,
                'module_type'   => config('constants.module_component_type.organization'),
                'invite_status' => config('constants.member_management_invite_status.accepted'),
            ])->pluck('email');

            return $memberManagement;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getMemberManagementByModuleAndEmail($moduleType, $email)
    {
        try {
            return MemberManagement::query()->where('module_type', $moduleType)->where('email', $email)->get();
        } catch (\Exception $exception) {
            UtilityHelper::logError($exception);

            return false;
        }
    }

    public static function totalActiveMembersCountBasedOnModuleIds($moduleIds, $moduleType)
    {
        try {
            $totalActiveMembersCountBasedOnModuleIds = MemberManagement::whereIn('module_id', $moduleIds)->where(['module_type' => $moduleType, 'invite_status' => '1'])->get();

            return $totalActiveMembersCountBasedOnModuleIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function getMembersBasedOnModule($moduleType)
    {
        try {
            $totalMembersBasedOnModule = MemberManagement::where(['module_type' => $moduleType, 'invite_status' => '1'])->get();

            return $totalMembersBasedOnModule;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function fetchMemberOrganizationIds($userEmail, $role, $inviteStatus)
    {
        try {
            $fetchMemberOrganizationIds = MemberManagement::where(['email' => $userEmail, 'role' => $role, 'invite_status' => $inviteStatus])->pluck('module_id');

            return $fetchMemberOrganizationIds;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function autoAssignedMemberFromAssociatedComponent($memberObj)
    {
        try {
            $member = MemberManagement::where(['module_id' => $memberObj['module_id'], 'email' => $memberObj['email'], 'module_type' => $memberObj['module_type']])->first();
            if ($member) {
                $member->type = $memberObj['type'];
                $member->invite_type = $memberObj['invite_type'];
                $member->inviter_id = $memberObj['inviter_id'];
                $member->auto_invite = $memberObj['auto_invite'];
                $member->invite_status = $memberObj['invite_status'];
                $member->invitee_name = $memberObj['invitee_name'];
                $member->email_status = $memberObj['email_status'];
                $member->is_associated_member = $memberObj['is_associated_member'];
                $member->associated_component = $memberObj['associated_component'];
                $member->associated_component_id = $memberObj['associated_component_id'];
                $member->save();
            } else {
                MemberManagement::create([
                    'uuid'                     => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                    'type'                     => $memberObj['type'],
                    'invite_type'              => $memberObj['invite_type'],
                    'module_id'                => $memberObj['module_id'],
                    'module_type'              => $memberObj['module_type'],
                    'inviter_id'               => $memberObj['inviter_id'],
                    'role'                     => $memberObj['role'],
                    'email'                    => $memberObj['email'],
                    'auto_invite'              => $memberObj['auto_invite'],
                    'invite_status'            => $memberObj['invite_status'],
                    'invitee_name'             => $memberObj['invitee_name'],
                    'email_status'             => $memberObj['email_status'],
                    'subject_line'             => $memberObj['subject_line'],
                    'email_body'               => $memberObj['email_body'],
                    'email_resend_status'      => $memberObj['email_resend_status'],
                    'is_associated_member'     => $memberObj['is_associated_member'],
                    'associated_component'     => $memberObj['associated_component'],
                    'associated_component_id'  => $memberObj['associated_component_id'],
                ]);
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
