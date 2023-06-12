<?php

namespace App\Helpers;

use App\Models\ChallengeProject;
use App\Models\MemberManagement;
use App\Models\ChallangeAssessment;
use App\Models\User;
use App\Models\Lab;
use Settings;
use App\Models\UserPoint;
use App\Models\Challange;
use App\Models\Favorite;
use App\Models\Project;
use Illuminate\Support\Facades\Event;
use Exception;
use App\Jobs\LabInvitedUserAutomaticallyInvitePodcast;
use Session;
use DB;
use Illuminate\Support\Facades\App;
use App\Helpers\NotificationHelper;
use App\Models\ModuleProgressStatus;
use Carbon\Carbon;

class MemberManagementHelper
{
    /* -----------------------------------------------------------------------------------------
     @Description: This function can get module data based on slug
     @Input: slug , moduleType
     @Output: return module data array
     -------------------------------------------------------------------------------------------- */
    public static function getModuleDataBySlug($slug, $moduleType)
    {
        if ($moduleType === 'lab') {
            return Lab::select('labs.id', 'labs.language', 'labs.organisation', 'labs.slug', 'labs.title', 'organisations.name as organisationName')
                ->join('organisations', 'organisations.id', '=', 'labs.organisation')
                ->where(['labs.slug'=> $slug,'labs.language'=> App::currentLocale()])
                ->first();
        } elseif ($moduleType === 'challenge') {
            return Challange::select('challanges.id', 'challanges.language', 'challanges.organisation', 'challanges.slug', 'challanges.title', 'organisations.name as organisationName')
                ->join('organisations', 'organisations.id', '=', 'challanges.organisation')
                ->where(['challanges.slug'=> $slug,'challanges.language'=> App::currentLocale()])
                ->first();
        }
    }

    /* -----------------------------------------------------------------------------------------
     @Description: This function can get members data with filter
     @Input: id
     @Output: return labMemberData array
     -------------------------------------------------------------------------------------------- */
    public static function getMembersData($request, $moduleId, $moduleType)
    {
        $memberData = MemberManagement::select('users.username', 'users.first_name', 'users.last_name', 'users.profile_image', 'member_management.*')
            ->leftJoin('users', 'users.id', '=', 'member_management.invitee_id')
            ->where(['member_management.module_id' => $moduleId, 'member_management.module_type' => $moduleType]);
        if (isset($request->searchname)) {
            if (filter_var(trim($request->searchname), FILTER_VALIDATE_EMAIL)) {
                $memberData = $memberData->Where('member_management.email', 'like', '%' . $request->searchname . '%');
            } else {
                $memberData = $memberData->Where('users.username', 'like', '%' . $request->searchname . '%');
            }
        }
        if (isset($request->email_status)) {
            $memberData = $memberData->where('email_status', $request->email_status);
        }

        return $memberData->orderBy('id', 'DESC')->paginate(10);
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can get project data with filter
    @Input: id
    @Output: return project array
     -------------------------------------------------------------------------------------------- */
    public static function getProjectData($moduleId)
    {
        return Project::select('projects.id', 'projects.user_id', 'projects.title', 'projects.team', 'projects.created_at', 'users.username as projectcreator')
            ->where(['projects.challenge_id' => $moduleId])
            ->leftJoin('users', 'users.id', '=', 'projects.user_id')
            ->orderBy('projects.id', 'DESC')
            ->paginate(20, ['*'], 'projects');
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can get project member data
    @Input: teamIds
    @Output: return user name
    -------------------------------------------------------------------------------------------- */
    public static function getProjectTeamMembers($teamIds)
    {
        if (!empty($teamIds)) {
            $explodedTeam = explode(',', $teamIds);
            return User::whereIn('id', $explodedTeam)->get()->implode('username', ',');
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can get lab members with filter
    @Input: id
    @Output: return organisation_id array
    -------------------------------------------------------------------------------------------- */
    public static function removeMembers($request)
    {
        // if remove single lab member
        if ($request->type == 'single') {
            $userData = MemberManagement::where(['id' => $request->memberId])->first();
            if (!empty($userData)) {
                MemberManagement::where(['id' => $request->memberId])->forceDelete();
                $message = __('notification.notification_mhbd');
            } else {
                $message = __('notification.notification_mnf');
            }
        }

        // if remove multiple lab members
        if ($request->type == 'multiple') {
            MemberManagement::whereIn('id', $request->memberId)->forceDelete();
            $message = __('notification.notification_samhbd');
        }

        return $message;
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can update invitation status after sent mail for lab
    @Input: invite_status, email_status, queueDetails,status
    -------------------------------------------------------------------------------------------- */
    public static function labInvitationStatusUpdate($invite_status, $email_status, $queueDetails, $status, $inviteStatus)
    {
        $privacy = '1';
        if ($queueDetails->privacy == 'private') {
            $privacy = '0';
        } else {
            $privacy = '1';
        }
        MemberManagement::where('id', $queueDetails->member_id)->update(['invite_status' => $invite_status, 'email_status' => $email_status, 'invitee_id' => $queueDetails->invitee_id, 'privacy' => $privacy]);
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can update invitation status after sent mail for challenge
    @Input: invite_status, email_status, queueDetails
    -------------------------------------------------------------------------------------------- */
    public static function challengeInvitationStatusUpdate($invite_status, $email_status, $queueDetails)
    {
        MemberManagement::where('id', $queueDetails->member_id)->update(['invite_status' => $invite_status, 'email_status' => $email_status, 'invitee_id' => $queueDetails->invitee_id]);

        // User Like, Favorite function
        if ($invite_status == 'accepted') {
            $data = Favorite::create(['ref_id' => $queueDetails->module_id, 'ref_type' => 'challange', 'user_id' => $queueDetails->invitee_id, 'likeit' => '1', 'favorite' => '1', 'is_follow' => '1']);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can update invitation status after fail email
    @Input: invite_status, email_status, queueDetails
    -------------------------------------------------------------------------------------------- */
    public static function failInvitationStatusUpdate($invite_status, $email_status, $queueDetails)
    {
        MemberManagement::where('id', $queueDetails->member_id)->update(['invite_status' => $invite_status, 'email_status' => $email_status]);
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can delete challenge eveluator from challenge assessment table and member management table
    @Input: request
    @Output: status message
    -------------------------------------------------------------------------------------------- */
    public static function removeEvaluatorMembers($request)
    {
        if ($request->type == 'challenge') {
            $assessmentData = ChallangeAssessment::select('id', 'members')->where('challenge_id', $request->moduleId)->first();
            if (!empty($assessmentData)) {
                MemberManagement::where(['id' => $request->memberId])->delete();
                $memberEmails = json_decode($assessmentData['members']);
                $updatedEmailList =[];
                if (!empty($memberEmails)) {
                    foreach ($memberEmails as $key => $memberEmail) {
                        if ($request->emailId != $memberEmail) {
                            $updatedEmailList[] = $memberEmail;
                        }
                    }
                    ChallangeAssessment::where('id', $assessmentData->id)->update(['members' => !empty($updatedEmailList) ? json_encode($updatedEmailList) : null]);
                    $message = __('notification.notification_emhbds');
                } else {
                    $message = __('notification.notification_emnf');
                }
            } else {
                $message = __('notification.notification_emnf');
            }
        }
        return $message;
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can add invited member from network
    @param: request
    -------------------------------------------------------------------------------------------- */
    public static function inviteFromNetwork($request)
    {
        $invitedMembers = array();
        if (!empty($request->inviteUsers)) {
            foreach ($request->inviteUsers as $inviteMember) {
                $inviteData['invite_type']  = $request->invite_type;
                $inviteData['module_id']    = (int) $request->module_id;
                $inviteData['module_type']  = $request->module_type;
                $inviteData['inviter_id']   = (int) $request->inviter_id;
                $inviteData['invitee_id']   = $inviteMember;
                $inviteData['subject_line'] = $request->subject_line;
                $inviteData['email_message'] = $request->email_message;
                $inviteData['auto_invite_status'] = $request->auto_invite_status;
                $inviteData['invite_status'] = 'pending';
                $inviteData['email_status'] = 'schedule';
                $inviteData['assign_role']  = $request->assign_role;
                $inviteData['challenge_auto_invite_from_lab']  = 0;
                $invitedMembers[] = $inviteData;
            }
            if (!empty($invitedMembers)) {
                $data = PlanSubscriptionHelper::getSubscribedPlanDetailForOrg($request->organisation);
                if ($data['subscriptionDetail']->subscriptionItems[0]->itemPriceId !=  config('chargebee.unlimited_plan')) {
                    $componentLimits = PlanSubscriptionHelper::getTotalLimits($request->organisation, 'userInvite');
                    $componentUsage = PlanSubscriptionHelper::getComponentUsage($request->organisation, 'userInvite');
                    $limitsLeft =  $componentLimits - $componentUsage;
                    $invitedMembersCount = count($invitedMembers);
                        if ($limitsLeft < $invitedMembersCount) {
                            return ['success' => false, 'error' => true, 'invalidEmailData' => [], 'alreadyExistEmailData' => [], 'message' => 'you have ' .$limitsLeft. ' credits left for user invite. Please add users according to the Pending limits' ];
                        }
                }
                if (MemberManagement::insert($invitedMembers)) {
                    $responce = ['success' => true, 'error' => false, 'alreadyExistNetworkData' => [], 'invalidNetworkData' => [], 'message' => __('notification.notification_mas')];
                }
            } else {
                $responce = ['success' => false, 'error' => true, 'alreadyExistNetworkData' => [], 'invalidNetworkData' => [], 'message' => __('notification.notification_ntcudini')];
            }
            return $responce;
        } else {
            return ['success' => false, 'error' => true, 'alreadyExistNetworkData' => [], 'invalidNetworkData' => [], 'message' => __('notification.notification_psaou')];
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can add invited member by email
    @param: request
    -------------------------------------------------------------------------------------------- */
    public static function inviteMemberByEmail($request)
    {
        $invitedMembers = array();
        $invalidEmailData = array();
        $alreadyExistEmailData = array();
        if (!empty($request->user_invite_email)) {
            $userInviteEmails = explode(',', $request->user_invite_email);
            foreach ($userInviteEmails as $inviteEmail) {
                if (filter_var(trim($inviteEmail), FILTER_VALIDATE_EMAIL)) {
                    if (!in_array(trim($inviteEmail), $invitedMembers)) {
                        if (!MemberManagement::where(['module_id' => (int) $request->module_id, 'module_type' => $request->module_type, 'email' => trim($inviteEmail)])->exists()) {
                            $inviteData['invite_type']  = $request->invite_type;
                            $inviteData['email']        = trim($inviteEmail);
                            $inviteData['module_id']    = (int) $request->module_id;
                            $inviteData['module_type']  = $request->module_type;
                            $inviteData['inviter_id']   = (int) $request->inviter_id;
                            $inviteData['subject_line'] = $request->subject_line;
                            $inviteData['email_message'] = $request->email_message;
                            $inviteData['auto_invite_status'] = $request->auto_invite_status;
                            $inviteData['invite_status'] = 'pending';
                            $inviteData['email_status'] = 'schedule';
                            $inviteData['assign_role']  = $request->assign_role;
                            $inviteData['challenge_auto_invite_from_lab']  = 0;
                            $invitedMembers[] = $inviteData;
                        } else {
                            $alreadyExistEmailData[] = $inviteEmail;
                            $responce = ['success' => false, 'error' => true, 'alreadyExistEmailData' => $alreadyExistEmailData, 'invalidEmailData' => $invalidEmailData, 'message' => __('notification.notification_peveiel')];
                        }
                    } else {
                        $alreadyExistEmailData[] = $inviteEmail;
                        $responce = ['success' => false, 'error' => true, 'alreadyExistEmailData' => $alreadyExistEmailData, 'invalidEmailData' => $invalidEmailData, 'message' => __('notification.notification_peveiel')];
                    }
                } else {
                    $invalidEmailData[] = $inviteEmail;
                    $responce = ['success' => false, 'error' => true, 'alreadyExistEmailData' => $alreadyExistEmailData, 'invalidEmailData' => $invalidEmailData, 'message' => __('notification.notification_peveiel')];
                }
            }
            if (!empty($invitedMembers)) {
                $data = PlanSubscriptionHelper::getSubscribedPlanDetailForOrg($request->organisation);
                if ($data['subscriptionDetail']->subscriptionItems[0]->itemPriceId !=  config('chargebee.unlimited_plan')) {
                $componentLimits = PlanSubscriptionHelper::getTotalLimits($request->organisation, 'userInvite');
                $componentUsage = PlanSubscriptionHelper::getComponentUsage($request->organisation, 'userInvite');
                $limitsLeft =  $componentLimits - $componentUsage;
                $invitedMembersCount = count($invitedMembers);
                    if ($limitsLeft < $invitedMembersCount) {
                        return ['success' => false, 'error' => true, 'invalidEmailData' => [], 'alreadyExistEmailData' => [], 'message' => 'you have ' .$limitsLeft. ' credits left for user invite. Please add users according to the Pending limits' ];
                    }
                }
                if (MemberManagement::insert($invitedMembers)) {
                    $responce = ['success' => true, 'error' => false, 'alreadyExistEmailData' => $alreadyExistEmailData, 'invalidEmailData' => $invalidEmailData, 'message' => __('notification.notification_edas')];
                }
            } else {
                $responce = ['success' => false, 'error' => true, 'alreadyExistEmailData' => $alreadyExistEmailData, 'invalidEmailData' => $invalidEmailData, 'message' => __('notification.notification_ntcedad')];
            }
            return $responce;
        } else {
            return ['success' => false, 'error' => true, 'alreadyExistEmailData' => $alreadyExistEmailData, 'invalidEmailData' => $invalidEmailData, 'message' => __('notification.notification_peeief')];
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can add invited member by email using csv
    @param: request
    -------------------------------------------------------------------------------------------- */
    public static function inviteByCsv($request)
    {
        $invitedMembers = array();
        $invalidEmailinvitedData = array();
        $alreadyInvitedEmailData = array();
        $csvEmailData = array();
        $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
        // If uploaded file is not a csv
        if (in_array($_FILES['user_invite_csv']['type'], $mimes)) {
            if (!empty($request->user_invite_csv)) {
                $csvResultData = array();
                if (($handle = fopen($request->user_invite_csv, "r")) !== false) {
                    $header = fgetcsv($handle, 0, ',');
                    $countheader = count($header);
                    while (($csvGetData = fgetcsv($handle, 1000, ",")) !== false) {
                        $csvResultData[] = $csvGetData;
                    }
                    fclose($handle);
                    // If csv not proper header formate
                    if ($countheader == 2  && in_array('Name', $header) && in_array('Email', $header)) {
                        if (!empty($csvResultData)) {
                            foreach ($csvResultData as $key => $csvData) {
                                // check if not a valid email
                                if (filter_var(trim($csvData[1]), FILTER_VALIDATE_EMAIL)) {
                                    // check if duplicate email in csv
                                    if (!in_array(trim($csvData[1]), $csvEmailData)) {
                                        // check if duplicate email in already invited data
                                        if (!MemberManagement::where(['module_id' => (int) $request->module_id, 'module_type' => $request->module_type, 'email' => trim($csvData[1])])->exists()) {
                                            $inviteData['invite_type']  = $request->invite_type;
                                            $inviteData['email']        = trim($csvData[1]);
                                            $inviteData['module_id']    = (int) $request->module_id;
                                            $inviteData['module_type']  = $request->module_type;
                                            $inviteData['inviter_id']   = (int) $request->inviter_id;
                                            $inviteData['subject_line'] = $request->subject_line;
                                            $inviteData['email_message'] = $request->email_message;
                                            $inviteData['auto_invite_status'] = $request->auto_invite_status;
                                            $inviteData['invite_status'] = 'pending';
                                            $inviteData['email_status']  = 'schedule';
                                            $inviteData['assign_role']   = $request->assign_role;
                                            $inviteData['challenge_auto_invite_from_lab']  = 0;
                                            $invitedMembers[] = $inviteData;
                                            $csvEmailData[] = $csvData[1];
                                        } else {
                                            $alreadyInvitedEmailData[] = $csvData[1];
                                            $responce = ['success' => false, 'error' => true, 'alreadyInvitedEmailData' => $alreadyInvitedEmailData, 'invalidEmailinvitedData' => $invalidEmailinvitedData, 'message' => __('notification.notification_teiycsvnv')];
                                        }
                                    } else {
                                        $alreadyInvitedEmailData[] = $csvData[1];
                                        $responce = ['success' => false, 'error' => true, 'alreadyInvitedEmailData' => $alreadyInvitedEmailData, 'invalidEmailinvitedData' => $invalidEmailinvitedData, 'message' => __('notification.notification_teiycsvnv')];
                                    }
                                } else {
                                    $invalidEmailinvitedData[] = $csvData[1];
                                    $responce = ['success' => false, 'error' => true, 'alreadyInvitedEmailData' => $alreadyInvitedEmailData, 'invalidEmailinvitedData' => $invalidEmailinvitedData, 'message' => __('notification.notification_teiycsvnv')];
                                }
                            }
                            if (!empty($invitedMembers)) {
                                $data = PlanSubscriptionHelper::getSubscribedPlanDetailForOrg($request->organisation);
                                if ($data['subscriptionDetail']->subscriptionItems[0]->itemPriceId !=  config('chargebee.unlimited_plan')) {
                                $componentLimits = PlanSubscriptionHelper::getTotalLimits($request->organisation, 'userInvite');
                                $componentUsage = PlanSubscriptionHelper::getComponentUsage($request->organisation, 'userInvite');
                                $limitsLeft =  $componentLimits - $componentUsage;
                                $invitedMembersCount = count($invitedMembers);
                                    if ($limitsLeft < $invitedMembersCount) {
                                        return ['success' => false, 'error' => true, 'invalidEmailData' => [], 'alreadyExistEmailData' => [], 'message' => 'you have ' .$limitsLeft. ' credits left for user invite. Please add users according to the Pending limits' ];
                                    }
                                }
                                if (MemberManagement::insert($invitedMembers)) {
                                    $responce = ['success' => true, 'error' => false, 'invalidEmailinvitedData' => $invalidEmailinvitedData, 'alreadyInvitedEmailData' => $alreadyInvitedEmailData, 'message' => __('notification.notification_csvdds')];
                                }
                            } else {
                                $responce = ['success' => false, 'error' => true, 'invalidEmailinvitedData' => $invalidEmailinvitedData, 'alreadyInvitedEmailData' => $alreadyInvitedEmailData, 'message' => __('notification.notification_ntcsvd')];
                            }
                            return $responce;
                        } else {
                            return ['success' => false, 'error' => true, 'invalidEmailinvitedData' => [], 'alreadyInvitedEmailData' => [], 'message' => __('notification.notification_tucsvfdni')];
                        }
                    } else {
                        return ['success' => false, 'error' => true, 'invalidEmailinvitedData' => [], 'alreadyInvitedEmailData' => [], 'message' => __('notification.notification_ycsvfdfrf')];
                    }
                }
            }
        } else {
            return ['success' => false, 'error' => true, 'invalidEmailinvitedData' => [], 'alreadyInvitedEmailData' => [], 'message' => __('notification.notification_tfumbf')];
        }
    }

    /**
     * This function use for get chedule and fail email data
     *
     * @param cheduleType,memberId
     */
    public static function getScheduleData()
    {
        $memberData = MemberManagement::select('member_management.id', 'member_management.module_type', 'member_management.invite_type', 'member_management.module_id', 'member_management.invitee_id', 'labs.title as labTitle', 'labs.slug as labSlug', 'labs.privacy as labsPrivacy', 'labs.image as labsImage', 'labs.language as labLanguage', 'member_management.email', 'userInvitee.email as userEmail', 'userInvitee.is_subscribe', 'userInvitee.name', 'userInvitee.username', 'userInviter.name as inviterName', 'member_management.invite_status', 'member_management.subject_line', 'member_management.email_message', 'member_management.auto_invite_status', 'challanges.title as challengeTitle', 'challanges.slug as challengeSlug', 'challanges.cover_image as challengeCoverImage', 'challanges.language as challengeLanguage', 'member_management.email_status')
            ->leftJoin('users as userInvitee', 'userInvitee.id', '=', 'member_management.invitee_id')
            ->leftJoin('users as userInviter', 'userInviter.id', '=', 'member_management.inviter_id')
            ->leftJoin('labs', 'labs.id', '=', 'member_management.module_id')
            ->leftJoin('challanges', 'challanges.id', '=', 'member_management.module_id');
        return $memberData->orderBy('member_management.id', 'ASC');
    }

    /**
     * This function used for send email to lab member which is invited by email
     *
     * @param memberData
     */
    public static function sentInviteLabByEmail($memberData)
    {
        $url = self::getRedirectUrl($memberData->auto_invite_status, $memberData->invite_type);
        Event::dispatch('send-invitation', array([
            'mail_template'     => 'invite_lab',
            "member_id"         => $memberData->id,
            "invitee_id"        => $memberData->invitee_id,
            "module_id"         => $memberData->module_id,
            "language"          => $memberData->labLanguage,
            "privacy"           => $memberData->labsPrivacy,
            "module_type"       => $memberData->module_type,
            "invite_type"       => $memberData->invite_type,
            "auto_invite_status" => $memberData->auto_invite_status,
            "url"               => $url,
            "cover_image"       => $memberData->labsImage,
            "lab_user"          => $memberData->inviterName,
            "username"          => 'Solver',
            'lab'               => $memberData->labTitle,
            "email_message"     => $memberData->email_message,
            "slug"              => $memberData->labSlug,
            'email'             => trim($memberData->email),
            'to_email'          => trim($memberData->email),
            'to_name'           => 'Solver',
            'fullname'          => 'Solver',
            'title'             => $memberData->labTitle,
            'subject_title'     => isset($memberData->subject_line) ? $memberData->subject_line : 'Invitation Email',
            'name'              => 'Solver',
            'isSentByEmail'     => 'yes'
        ]));
    }

    /**
     * This function used for send email to lab member which is invited from network
     *
     * @param memberData
     */
    public static function sentInviteLabFromNetwork($memberData)
    {
        $url = self::getRedirectUrl($memberData->auto_invite_status, $memberData->invite_type);
        Event::dispatch('send-invitation', array([
                'mail_template' => 'invite_lab',
                "member_id"     => $memberData->id,
                "invitee_id"    => $memberData->invitee_id,
                "module_id"     => $memberData->module_id,
                "language"      => $memberData->labLanguage,
                "privacy"       => $memberData->labsPrivacy,
                "module_type"   => $memberData->module_type,
                "invite_type"   => $memberData->invite_type,
                "auto_invite_status" => $memberData->auto_invite_status,
                "url"           => $url,
                "cover_image"   => $memberData->labsImage,
                "lab_user"      => $memberData->inviterName,
                "username"      => $memberData->username,
                'lab'           => $memberData->labTitle,
                "email_message" => $memberData->email_message,
                "slug"          => $memberData->labSlug,
                'email'         => trim($memberData->userEmail),
                'to_email'      => trim($memberData->userEmail),
                'to_name'       => $memberData->username,
                'fullname'      => $memberData->name,
                'title'         => $memberData->labTitle,
                'subject_title' => isset($memberData->subject_line) ? $memberData->subject_line : 'Invitation Email',
                'name'          => $memberData->name,
                'isSentByEmail' => 'no'
            ]));
    }

    /**
     * This function use for get unaccepted module data
     *
     * @param userEmail ,userID ,moduleType
     */
    public static function getInvitedModule($userEmail, $userID, $moduleType)
    {
        return MemberManagement::select('member_management.id as memberId', 'member_management.module_id', 'labs.slug', 'labs.title', 'labs.user_id', 'labs.id', 'labs.user_count', 'labs.mediaType', 'labs.description', 'labs.image', 'users.name', 'users.username', 'users.profile_image', 'member_management.created_at')
            ->where(['member_management.module_type' => $moduleType, 'member_management.email_status' => 'sent', 'member_management.auto_invite_status' => 'manual', 'member_management.invite_status' => 'pending','labs.language' => App::currentLocale()])
            ->join('labs', 'labs.id', '=', 'member_management.module_id')
            ->leftJoin('users', 'users.id', '=', 'member_management.inviter_id')
            ->where(function ($q) use ($userEmail, $userID) {
                $q->orWhere(['member_management.email' => $userEmail, 'member_management.invitee_id' => $userID]);
            })->orderBy('id', 'ASC')->get();
    }

    /**
     * This function use for get accepted module data
     *
     * @param  userID ,moduleType
     */
    public static function getAcceptedModule($userID, $moduleType)
    {
        return MemberManagement::select('member_management.id as memberId', 'labs.slug', 'labs.title', 'labs.user_id', 'labs.id', 'labs.user_count', 'labs.mediaType', 'labs.description', 'labs.image', 'member_management.created_at')
            ->where(['member_management.module_type' => $moduleType, 'member_management.invite_status' => 'accepted','labs.language' => App::currentLocale()])
            ->join('labs', 'labs.id', '=', 'member_management.module_id')
            ->where(function ($q) use ($userID) {
                $q->where(['member_management.invitee_id' => $userID]);
            })->orderBy('id', 'ASC')->get();
    }

    /**
     * This function use for get unaccepted Challenge module data
     *
     * @param userEmail ,userID ,moduleType
     */
    public static function getInvitedChallengeModule($userEmail, $userID, $moduleType)
    {
        return MemberManagement::select('member_management.id as memberId', 'member_management.module_id', 'challanges.slug', 'challanges.cover_image', 'challanges.title', 'challanges.user_id', 'challanges.id', 'challanges.user_count', 'users.name', 'users.username', 'users.profile_image')
            ->where(['member_management.module_type' => $moduleType, 'member_management.email_status' => 'sent', 'member_management.auto_invite_status' => 'manual', 'member_management.invite_status' => 'pending','challanges.language' => App::currentLocale()])
            ->join('challanges', 'challanges.id', '=', 'member_management.module_id')
            ->leftJoin('users', 'users.id', '=', 'member_management.inviter_id')
            ->where(function ($q) use ($userEmail, $userID) {
                $q->orWhere(['member_management.email' => $userEmail, 'member_management.invitee_id' => $userID]);
            })->orderBy('id', 'ASC')->get();
    }

    /**
     * This function use for get accepted and Submitted Challenge module data
     *
     * @param  userID ,moduleType
     */
    public static function getChallengeModule($userID, $moduleType)
    {
        $memberChallengeId = MemberManagement::where(['member_management.module_type' => $moduleType, 'member_management.invitee_id' => $userID, 'member_management.invite_status' => 'accepted'])->pluck('module_id')->toArray();

        $projectChallengeId = ChallengeProject::where(['challange_projects.user_id' => $userID])->pluck('challange_id')->toArray();
        $arrayChallengeId = array_unique(array_merge($memberChallengeId, $projectChallengeId));

        return Challange::select('challanges.id as memberId', 'challanges.slug', 'challanges.dates', 'challanges.flexibleExpireDate', 'challanges.title', 'challanges.cover_image', 'challanges.user_id', 'challanges.id', 'challanges.description', 'challanges.user_count')->where('challanges.language', App::currentLocale())->whereIn('id', $arrayChallengeId)->orderBy('id', 'DESC')->get();
    }

    /**
     * This function used for send email to Challenge member which is invited by email
     *
     * @param memberData
     */
    public static function sentInviteChallengeByEmail($memberData)
    {
        $url = self::getRedirectUrl($memberData->auto_invite_status, $memberData->invite_type);
        Event::dispatch('send-invitation', array([
            'mail_template'     => 'invite_challenge',
            "member_id"         => $memberData->id,
            "invitee_id"        => $memberData->invitee_id,
            "module_id"         => $memberData->module_id,
            "language"          => $memberData->challengeLanguage,
            "privacy"           => $memberData->privacy,
            "module_type"       => $memberData->module_type,
            "invite_type"       => $memberData->invite_type,
            "auto_invite_status" => $memberData->auto_invite_status,
            "url"               => $url,
            "cover_image"       => $memberData->challengeCoverImage,
            "challenge_user"    => $memberData->inviterName,
            "username"          => 'Solver',
            "type"              => 'challenge',
            'challenge'         => $memberData->challengeTitle,
            "email_message"     => $memberData->email_message,
            "slug"              => $memberData->challengeSlug,
            'email'             => trim($memberData->email),
            'to_email'          => trim($memberData->email),
            'to_name'           => 'Solver',
            'fullname'          => 'Solver',
            'title'             => $memberData->challengeTitle,
            'subject_title'     => isset($memberData->subject_line) ? $memberData->subject_line : 'Invitation Email for challenge',
            'name'              => 'Solver',
            'isSentByEmail'     => 'yes'
        ]));
    }

    /**
     * This function used for send email to Challenge member which is invited from network
     *
     * @param memberData
     */
    public static function sentInviteChallengeFromNetwork($memberData)
    {
        $url = self::getRedirectUrl($memberData->auto_invite_status, $memberData->invite_type);
        Event::dispatch('send-invitation', array([
                'mail_template' => 'invite_challenge',
                "member_id"     => $memberData->id,
                "invitee_id"    => $memberData->invitee_id,
                "module_id"     => $memberData->module_id,
                "language"      => $memberData->challengeLanguage,
                "privacy"       => $memberData->privacy,
                "module_type"   => $memberData->module_type,
                "invite_type"   => $memberData->invite_type,
                "auto_invite_status" => $memberData->auto_invite_status,
                "url"           => $url,
                "cover_image"   => $memberData->challengeCoverImage,
                "challenge_user" => $memberData->inviterName,
                "username"      => $memberData->username,
                "type"          => 'challenge',
                'challenge'     => $memberData->challengeTitle,
                "email_message" => $memberData->email_message,
                "slug"          => $memberData->challengeSlug,
                'email'         => trim($memberData->userEmail),
                'to_email'      => trim($memberData->userEmail),
                'to_name'       => $memberData->username,
                'fullname'      => $memberData->name,
                'title'         => $memberData->challengeTitle,
                'subject_title' => !empty($memberData->subject_line) ? $memberData->subject_line : __('labels.labels_yhbi'),
                'name'          => $memberData->name,
                'isSentByEmail'     => 'no'
            ]));
    }

    /**
     * This function can return redirect url.
     *
     * @param auto_invite_status,invite_type
     */
    public static function getRedirectUrl($auto_invite_status, $invite_type)
    {
        if ($auto_invite_status ==='manual' && $invite_type ==='email') {
            $route = route('register');
        } elseif ($auto_invite_status ==='manual' && $invite_type ==='network') {
            $route = route('login');
        } elseif ($auto_invite_status ==='auto' && $invite_type ==='email') {
            $route = route('register');
        } elseif ($auto_invite_status ==='auto' && $invite_type ==='network') {
            $route = route('login');
        } else {
            $route = route('userDashboardReport');
        }
        return $route;
    }

    /**
     * This function can send reject email for requested user
     *
     * @param memberData
     */
    public static function requestRejectionMail($labUsersTableId, $emailTemplate)
    {
        $labUserData = self::getScheduleData()->where('member_management.lab_users_id', $labUsersTableId)->first();
        if (!empty($labUserData)) {
            Event::dispatch('send-template', array([
                    'mail_template'     => $emailTemplate,
                    'sender'            => auth()->user()->name,
                    'name'              => auth()->user()->name,
                    'sender_image'      => auth()->user()->profile_image,
                    'time'              => date('M d,Y H:mA', strtotime($labUserData->created_at)) . ' EST',
                    "username"          => $labUserData->name,
                    'email'             => $labUserData->userEmail,
                    'to_email'          => $labUserData->userEmail,
                    'to_name'           => $labUserData->username,
                    'fullname'          => $labUserData->name,
                    'sender_url'        => route('getProfile', [auth()->user()->username]),
                    'lab_url'           => route('userlab.show', [$labUserData->labSlug]),
                    'lab'               => $labUserData->labTitle
                ]));
        }
    }

    /**
     * This function used for insert evaluator data for challenge assesment
     *
     * @param @chalengeId , @action
     */
    public static function assessmentDataToMemberMenagement($challengeId, $action)
    {
        try {
            $data = User::where(['status' => 'active', 'is_verify' => '1'])->latest()->pluck('email')->toarray();
            $assessmentData = ChallangeAssessment::where('challenge_id', $challengeId)->first();
            $org_id = Challange::where('id', $challengeId)->pluck('organisation')->first();
            if (!empty($assessmentData) && $assessmentData['assessment_type'] == 'closed') {
                $membersArray = [];
                if (!empty($assessmentData['members'])) {
                    $members = json_decode($assessmentData['members']);
                    $networkMemberList= array_intersect($members, $data);
                    if (isset($networkMemberList) && count($networkMemberList) > 0) {
                        foreach ($networkMemberList as $member) {
                            $userData = User::where('email', $member)->latest()->first('id');
                            $membersData['invite_type'] = 'network';
                            $membersData['module_id'] = $assessmentData['challenge_id'];
                            $membersData['module_type'] = 'challenge';
                            $membersData['inviter_id']  = auth()->user()->id;
                            $membersData['invitee_id']  = !empty($userData) ? $userData->id : null;
                            $membersData['email'] = $member;
                            $membersData['invite_status'] = 'invited';
                            $membersData['email_status'] = 'other';
                            $membersData['auto_invite_status'] = 'other';
                            $membersData['assign_role'] = 'user';
                            $membersData['is_evaluator'] = 1;
                            $membersArray[] = $membersData;
                        }
                    }
                    $emailMemberList= array_diff($members, $data);
                    if (isset($emailMemberList) && count($emailMemberList) > 0) {
                        foreach ($emailMemberList as $member) {
                            $userData = User::where('email', $member)->latest()->first('id');
                            $membersData['invite_type'] = 'email';
                            $membersData['module_id'] = $assessmentData['challenge_id'];
                            $membersData['module_type'] = 'challenge';
                            $membersData['inviter_id']  = auth()->user()->id;
                            $membersData['invitee_id']  = !empty($userData) ? $userData->id : null;
                            $membersData['email'] = $member;
                            $membersData['invite_status'] = 'invited';
                            $membersData['email_status'] = 'other';
                            $membersData['auto_invite_status'] = 'other';
                            $membersData['assign_role'] = 'user';
                            $membersData['is_evaluator'] = 1;
                            $membersArray[] = $membersData;
                        }
                    }
                }

                if ($action == 'update') {
                    MemberManagement::where(['module_id' => $assessmentData['challenge_id'], 'is_evaluator' => 1])->delete();
                }
                $data = PlanSubscriptionHelper::getSubscribedPlanDetailForOrg($org_id);
                if ($data['subscriptionDetail']->subscriptionItems[0]->itemPriceId !=  config('chargebee.unlimited_plan')) {
                    $componentLimits = PlanSubscriptionHelper::getTotalLimits($org_id, 'userInvite');
                    $componentUsage = PlanSubscriptionHelper::getComponentUsage($org_id, 'userInvite');
                    $limitsLeft =  $componentLimits - $componentUsage;
                    $invitedMembersCount = count($membersArray);
                    if ($limitsLeft < $invitedMembersCount) {
                        return $limitsLeft;
                    }
                }
                MemberManagement::insert($membersArray);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * This function use for accept , decline , cancel request from lab member management
     *
     * @param  request ,labData,action
     */
    public static function labRequestAcceptReject($request, $labData, $action)
    {
        if ($action === 'request') {
            // request for private lab
            if ($labData['public'] == '0') {
                $memberData = [
                    'invite_type'   => 'network',
                    'module_id'     => (int) $labData['lab_id'],
                    'module_type'   => 'lab',
                    'inviter_id'    => (int) $labData['user_id'],
                    'invitee_id'    => (int) $labData['invitee_id'],
                    'email'         => null,
                    'invite_status' => 'invited',
                    'email_status'  => 'other',
                    'auto_invite_status' => 'other',
                    'assign_role'   => 'user',
                    'is_join_request' => 1,
                    'join_request_status' => 0,
                    'privacy'       => 0,
                    //'lab_users_id'  => $labData['id'],
                    'challenge_auto_invite_from_lab'  => 0,
                ];
            } elseif ($labData['public'] == '1') {
                // request for public lab
                $memberData = [
                    'invite_type'   => 'network',
                    'module_id'     => (int) $labData['lab_id'],
                    'module_type'   => 'lab',
                    'inviter_id'    => (int) $labData['user_id'],
                    'invitee_id'    => (int) $labData['invitee_id'],
                    'email'         => null,
                    'invite_status' => 'accepted',
                    'email_status'  => 'other',
                    'auto_invite_status' => 'other',
                    'assign_role'   => 'user',
                    'is_join_request' => 1,
                    'join_request_status' => 1,
                    'privacy'       => 1,
                    //'lab_users_id'  => $labData['id'],
                    'challenge_auto_invite_from_lab'  => 0,
                ];
            }
            MemberManagement::create($memberData);
        } elseif ($action === 'accept') {
            if (empty($labData)) {
                // if request from member management page
                MemberManagement::where(['id' => $request->memberId])->update(['join_request_status' => 1, 'invite_status' => 'accepted', 'inviter_id' => auth()->user()->id]);

                self::requestRejectionMail($request->labUsersId, 'lab_request_accept');
            } else {
                MemberManagement::where(['lab_users_id' => $labData['id']])->update(['join_request_status' => 1, 'invite_status' => 'accepted', 'inviter_id' => auth()->user()->id]);
                self::requestRejectionMail($request->labUsersId, 'lab_request_accept');
            }
        } elseif ($action === 'decline') {
            if (empty($labData)) {
                // if request from member management page
                MemberManagement::where(['id' => $request->memberId])->update(['join_request_status' => 2, 'invite_status' => 'declined']);
                self::requestRejectionMail($request->labUsersId, 'lab_request_reject');
            } else {
                MemberManagement::where(['lab_users_id' => $labData['id']])->update(['join_request_status' => 2, 'invite_status' => 'declined', 'inviter_id' => auth()->user()->id]);
                self::requestRejectionMail($labData['id'], 'lab_request_reject');
            }
        } elseif ($action === 'cancel') {
            MemberManagement::where(['id' => $request->memberId])->delete();
            ModuleProgressStatus::where(['module_id' => $request->lab_id, 'user_id' => auth()->user()->id])->forcedelete();
            // $data = MemberManagement::where(['lab_users_id' => $labData['id']])->first();
            // if ($data !== null) {
            //     $data->forceDelete();
            // }
            //MemberManagement::where(['lab_users_id' => $labData['id']])->update(['join_request_status' => 3, 'invite_status' => 'declined', 'inviter_id' => auth()->user()->id]);
            //self::requestRejectionMail($labData['id'], 'lab_request_cancel');
        }
    }

     /**
     * This function use for accept , decline , cancel request from chalenge member management
     *
     * @param  request ,challengeDataArry,action
     */
    
    public static function chalRequestAcceptReject($request, $challengeDataArry, $action)
    {
        if ($action === 'request') {
            // request for private challenge
            $memberData = [
                'invite_type'   => 'network',
                'module_id'     => (int) $challengeDataArry['chl_id'],
                'module_type'   => 'challenge',
                'inviter_id'    => (int) $challengeDataArry['user_id'],
                'invitee_id'    => (int) $challengeDataArry['invitee_id'],
                'email'         => null,
                'invite_status' => 'invited',
                'email_status'  => 'other',
                'auto_invite_status' => 'other',
                'assign_role'   => 'user',
                'is_join_request' => 1,
                'join_request_status' => 0,
                'privacy'       => 0,
                'challenge_auto_invite_from_lab'  => 0,
            ];
            MemberManagement::create($memberData);
        } elseif ($action === 'accept') {
            if (empty($challengeDataArry)) {
                // if request accepting from member management page
                MemberManagement::where(['id' => $request->memberId])->update(['join_request_status' => 1, 'invite_status' => 'accepted', 'inviter_id' => auth()->user()->id]);
            }
        } elseif ($action === 'decline') {
            if (empty($challengeDataArry)) {
                // if request declined from member management page
                MemberManagement::where(['id' => $request->memberId])->update(['join_request_status' => 2, 'invite_status' => 'declined']);
            }
        } elseif ($action === 'cancel') {
            MemberManagement::where(['id' => $request->memberId])->delete();
        }
    }

    /**
     * This function can change status after verify there account
     *
     * @param user
     */
    public static function updateStatusInvited($user)
    {
        $member = MemberManagement::where(['email' => trim($user->email), 'email_status' => 'sent', 'invite_status' => 'pending', 'auto_invite_status' => 'auto'])->orderBy('id', 'DESC')->first();
        if (!empty($member)) {
            self::updateMemberInvitationActionData($member->module_type, $member->id, 'accepted', $user->id);
        }
    }

    /**
     * This function can lab invited user , automatically invite them to all the challenges associated to the lab
     *
     * @param user
     */
    public static function automaticallyInviteUserToChallengesAssociatedToLab($moduleId, $inviteType, $inviterId, $inviteeId)
    {
        $ifExist = MemberManagement::where(['module_type' => 'challenge', 'module_id' => $moduleId, 'inviter_id' => $inviterId, 'invitee_id' => $inviteeId])->first();
        if (empty($ifExist)) {
            MemberManagement::create(['module_type' => 'challenge', 'invite_type' => $inviteType, 'module_id' => $moduleId, 'inviter_id' => $inviterId, 'invitee_id' => $inviteeId, 'email' => null, 'invite_status' => 'accepted', 'email_status' => 'other', 'auto_invite_status' => 'other', 'assign_role' => 'user', 'is_evaluator' => 0, 'challenge_auto_invite_from_lab' => $moduleId]);
        }
    }

    /**
     * This function can automatically invtie user to challenges associated to lab after edit challenge
     *
     * @param user
     */
    public static function automaticallyInviteUserToChallengesAssociatedToLabAfterEditChallenge($challenge, $associateLab)
    {
        if (!empty($challenge) && !empty($associateLab)) {
            MemberManagement::where(['challenge_auto_invite_from_lab' => $challenge['id'], 'module_type' => 'challenge', 'invite_status' => 'accepted', 'email_status' => 'other', 'is_evaluator' => '0'])->forceDelete();
            LabInvitedUserAutomaticallyInvitePodcast::dispatch(['challengeId' => $challenge['id'], 'associateLabId' => $associateLab]);
        }
    }

    /**
     * This function can invite member by based on invite type
     *
     * @param request
     */
    public static function inviteMembers($request, $isRequired)
    {
        $emailResponce = [];
        $csvResponce = [];
        $networkResponce = [];
        $inviteByEmailResponce = [];
        $inviteNetworkResponce = [];
        // if user invited By email
        if ($request->invite_type == 'email') {
            if (!empty($request->user_invite_email) || !empty($request->user_invite_csv)) {
                if (!empty($request->user_invite_email)) {
                    $emailResponce = self::inviteMemberByEmail($request);
                }
                if (!empty($request->user_invite_csv)) {
                    $csvResponce = self::inviteByCsv($request);
                }
            } else {
                if ($isRequired == 'yes') {
                    $inviteByEmailResponce = ['success' => false, 'error' => true, 'message' => __('notification.notification_peeouc')];
                }
            }
        // if user invited from network
        } elseif ($request->invite_type == 'network') {
            if (!empty($request->inviteUsers)) {
                $networkResponce = self::inviteFromNetwork($request);
            } else {
                if ($isRequired == 'yes') {
                    $inviteNetworkResponce = ['success' => false, 'error' => true, 'message' => __('notification.notification_psu')];
                }
            }
        }
        return ['emailResponce' => $emailResponce, 'csvResponce' => $csvResponce, 'networkResponce' => $networkResponce, 'inviteByEmailResponce' => $inviteByEmailResponce, 'inviteNetworkResponce' => $inviteNetworkResponce];
    }

    /**
     * This function set alert message after invite
     *
     * @param request
     */
    public static function setAlertMessage($inviteMemberResponce)
    {
        if (!empty($inviteMemberResponce['emailResponce'])) {
            // start If get error while enter email
            if (!empty(array_filter($inviteMemberResponce['emailResponce']['invalidEmailData'], 'strlen'))) {
                $invalidEmailinvited = implode(', ', $inviteMemberResponce['emailResponce']['invalidEmailData']);
                Session::flash('invalidEmailinvited', $invalidEmailinvited);
            }
            if (!empty(array_filter($inviteMemberResponce['emailResponce']['alreadyExistEmailData'], 'strlen'))) {
                $alreadyInvitedEmail = implode(', ', array_filter($inviteMemberResponce['emailResponce']['alreadyExistEmailData'], 'strlen'));
                Session::flash('alreadyInvitedEmail', $alreadyInvitedEmail);
            }
            $alertEmailClass = $inviteMemberResponce['emailResponce']['success'] ? 'alert-success' : 'alert-danger';
            Session::flash($alertEmailClass, $inviteMemberResponce['emailResponce']['message']);
            // end get error while enter email
        }
        if (!empty($inviteMemberResponce['csvResponce'])) {
            // start If get error while upload csv
            if (!empty(array_filter($inviteMemberResponce['csvResponce']['invalidEmailinvitedData'], 'strlen'))) {
                $invalidEmailinvited = implode(', ', $inviteMemberResponce['csvResponce']['invalidEmailinvitedData']);
                Session::flash('invalidCsvEmailinvited', $invalidEmailinvited);
            }
            if (!empty(array_filter($inviteMemberResponce['csvResponce']['alreadyInvitedEmailData'], 'strlen'))) {
                $alreadyInvitedEmail = implode(', ', array_filter($inviteMemberResponce['csvResponce']['alreadyInvitedEmailData'], 'strlen'));
                Session::flash('alreadyInvitedCsvEmail', $alreadyInvitedEmail);
            }
            $alertCsvClass = $inviteMemberResponce['csvResponce']['success'] ? 'alert-success' : 'alert-danger';
            Session::flash($alertCsvClass, $inviteMemberResponce['csvResponce']['message']);
            // end get error while upload csv
        }
        if (!empty($inviteMemberResponce['inviteByEmailResponce'])) {
            // if not add email or not upload csv
            Session::flash('alert-danger', $inviteMemberResponce['inviteByEmailResponce']['message']);
        }
        if (!empty($inviteMemberResponce['inviteNetworkResponce'])) {
            // if not a select user in from network
            Session::flash('alert-danger', $inviteMemberResponce['inviteNetworkResponce']['message']);
        }
        if (!empty($inviteMemberResponce['networkResponce'])) {
            // start If get error while invite by network
            $alertCsvClass = $inviteMemberResponce['networkResponce']['success'] ? 'alert-success' : 'alert-danger';
            Session::flash($alertCsvClass, $inviteMemberResponce['networkResponce']['message']);
            // end get error while invite by network
        }
    }


    public static function inviteMembersFromChannelAPI($value, $lab)
    {
        try {
            DB::beginTransaction();
            $invitedMembers = [];

            $value = (object) $value;
            $user= User::where('magnet_user_id', $value->id)->first();

            if ($user) {
                if (!MemberManagement::where(['module_id' => (int) $lab->id, 'module_type' => 'lab', 'invitee_id' => $user->id])->exists()) {
                    $inviteData['invite_type']  = 'network';
                    $inviteData['email']        = $user->email;
                    $inviteData['module_id']    = (int) $lab->id;
                    $inviteData['module_type']  = 'lab';
                    $inviteData['inviter_id']   = (int) $lab->user_id;
                    $inviteData['invitee_id']   = $user->id;
                    $inviteData['subject_line'] = 'Invitation to Prepr '.$lab->title;
                    $inviteData['email_message'] = 'Welcome to the '.$lab->title.'! You will find a lot of the key information here, including the relevant challenges, resources, and discussion. Check back regularly for updates.';
                    $inviteData['auto_invite_status'] = 'manual';
                    $inviteData['invite_status'] = 'pending';
                    $inviteData['email_status'] = 'schedule';
                    $inviteData['assign_role']  = 'user';
                    $inviteData['challenge_auto_invite_from_lab']  = 0;
                    $invitedMembers[] = $inviteData;
                } else {
                    return (object) [
                        'message' => 'The specific user is already assigned to this content',
                        'status' => false
                    ];
                }
            } else {
                if (!MemberManagement::where(['module_id' => (int) $lab->id, 'module_type' => 'lab', 'email' => trim($value->email)])->exists()) {
                    $inviteData['invite_type']  = 'email';
                    $inviteData['email']        = $value->email;
                    $inviteData['module_id']    = (int) $lab->id;
                    $inviteData['module_type']  = 'lab';
                    $inviteData['inviter_id']   = $lab->user_id;
                    $inviteData['invitee_id']   = null;
                    $inviteData['subject_line'] = 'Invitation to Prepr '.$lab->title;
                    $inviteData['email_message'] = 'Welcome to the '.$lab->title.'! You will find a lot of the key information here, including the relevant challenges, resources, and discussion. Check back regularly for updates.';
                    $inviteData['auto_invite_status'] = 'manual';
                    $inviteData['invite_status'] = 'pending';
                    $inviteData['email_status'] = 'schedule';
                    $inviteData['assign_role']  = 'user';
                    $inviteData['challenge_auto_invite_from_lab']  = 0;
                    $invitedMembers[] = $inviteData;
                } else {
                    return (object) [
                        'message' => 'The specific user is already assigned to this content',
                        'status' => false
                    ];
                }
            }
            if (!empty($invitedMembers)) {
                MemberManagement::insert($invitedMembers);
                DB::commit();
                return (object) [
                        'status' => true
                ];
            }
        } catch (\Exception $ex) {
            DB::rollback();
            return (object) [
                        'message' => 'PREPR Internal Server Error',
                        'status' => false
            ];
        }
    }

    /**
     * This function can update member invitation action
     *
     * @param user
     */
    public static function updateMemberInvitationActionData($moduleType, $memberId, $status, $inviteeId)
    {
        if ($moduleType == "lab" || 'challenge') {
            if ($status == "accepted") {
                MemberManagement::where('id', $memberId)->update(['invite_status' => 'accepted','invitee_id' => $inviteeId]);
                if (auth()->user()->is_lab_joined === 'no') {
                    User::where('id', $inviteeId)->update(['is_lab_joined' => 'yes']);
                }
                if ($moduleType =='challenge') {
                    if (!empty($memberId)) {
                        $memberdata = MemberManagement::select('member_management.id as memberId', 'member_management.module_id', 'member_management.inviter_id', 'challanges.title')
                                                    ->where(['member_management.id' => $memberId])
                                                    ->join('challanges', 'challanges.id', '=', 'member_management.module_id')
                                                    ->first();
                        // Send notification for accept challenge for inviter
                        NotificationHelper::addNotification($memberdata->inviter_id, $memberdata->inviter_id, 'challenge', '0', 'challenge_join_notification', '', '', '', '', '', ['user_name' => auth()->user()->name, 'title' => $memberdata->title]);
                        // Send notification for join challenge successfully
                        NotificationHelper::addNotification($inviteeId, $inviteeId, 'challenge', '0', 'challenge_join_success_notification', '', '', '', '', '', ['title' => $memberdata->title]);
                    }
                }
            } elseif ($status == "rejected") {
                MemberManagement::where('id', $memberId)->update(['invite_status' => 'declined']);
            }
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can get general members data for member listing on lab details page
    @Input: labid
    -------------------------------------------------------------------------------------------- */
    public static function getGeneralMembers($moduleId, $moduleType)
    {
        return MemberManagement::select('users.username as username', 'users.profile_image', 'users.name', 'member_management.invitee_id as invitee_id', 'member_management.assign_role', 'users.email', 'users.id')
            ->leftJoin('users', 'users.id', '=', 'member_management.invitee_id')
            ->whereNotNull('users.username')
            ->where(['member_management.module_id' => $moduleId, 'member_management.module_type' => $moduleType, 'member_management.invite_status' => 'accepted'])
            ->orderBy('member_management.id', 'desc')
            ->get();
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can check member joined or not
    @Input: labid
     -------------------------------------------------------------------------------------------- */
    public static function checkMemberIsJoined($moduleId, $moduleType, $inviteeId)
    {
        return MemberManagement::select('id')->where(['module_type'=> $moduleType,'invite_status' => 'accepted','module_id' => $moduleId,'invitee_id' => $inviteeId])->first();
    }

    /* -----------------------------------------------------------------------------------------
    @Description: This function can get all request which is not accepted
    @Input: labid
    -------------------------------------------------------------------------------------------- */
    public static function getJoinRequest($moduleId, $moduleType)
    {
        return MemberManagement::select('users.username as username', 'users.profile_image', 'users.name', 'users.first_name', 'users.last_name', 'member_management.invitee_id as invitee_id')
            ->leftJoin('users', 'users.id', '=', 'member_management.invitee_id')
            ->where(['member_management.module_id' => $moduleId, 'member_management.module_type' => $moduleType,'invite_status' => 'pending','join_request_status' => 1])
            ->orderBy('member_management.id', 'desc')
            ->get();
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function can send email after join request
    @input: lab_id
    -------------------------------------------------------------------------------------------- */
    public static function joinRequestUserActivity($lab, $action)
    {
        if (!empty($lab)) {
            if ($action ==='request') {
                // Create point after join lab
                $point = Settings::get(config('points.lab_join'));
                $user_points = UserPoint::create(['type' => 'lab_join', 'date' => date('Y-m-d'), 'user_id' => auth()->user()->id, 'point' => $point]);

                // Send notification for join lab
                NotificationHelper::addNotification(auth()->user()->id, auth()->user()->id, 'lab', '0', 'lab_join_notification', '', '', '', '', '', ['lab_point' => $point ,'lab_title' => $lab->title]);
                // Send email only after join request also when lab is private
                if ($lab->privacy === 'private') {
                    Event::dispatch('send-template', array([
                        'mail_template' => 'lab_request',
                        'sender'        => auth()->user()->name,
                        'name'          => auth()->user()->name,
                        'sender_image'  => auth()->user()->profile_image,
                        'time'          => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now(), 'America/Toronto') . ' EST',
                        "username"      => $lab->name,
                        'email'         => $lab->emaiId,
                        'to_email'      => $lab->emaiId,
                        'to_name'       => $lab->username,
                        'fullname'      => $lab->name,
                        'sender_url'    => route('getProfile', [auth()->user()->username]),
                        'lab_url'       => route('userlab.show', [$lab->slug]),
                        'lab'           => $lab->title
                    ]));
                }
            } elseif ($action ==='cancel') {
                // Remove point after cancel request
                $point = Settings::get(config('points.lab_join'));
                $user_points = UserPoint::where(['type' => 'lab_join', 'user_id' => auth()->user()->id, 'point' => $point])->first();
                if ($user_points) {
                    $user_points->forceDelete();
                }
                // Send notification for cancel request
                NotificationHelper::addNotification(auth()->user()->id, auth()->user()->id, 'lab', '0', 'lab_left_notification', '', '', '', '', '', ['lab_title' => $lab->title]);
            }
        }
    }
}
