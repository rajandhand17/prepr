<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ProjectMemberManagement;
use App\Notifications\InviteMemberNotification;
use App\Services\UserService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ProjectMemberManagementService
{
    public function getProjectBasedParticipants($projectData, $request)
    {
        try {
            $module_type = null;
            $projectParticipantCollectionObject = ProjectMemberManagement::where('project_id', $projectData->id);
            $projectParticipantCollectionObject = self::filterUserList($projectParticipantCollectionObject, $request);

            return $projectParticipantCollectionObject->paginate(config('site-settings.pagination_per_page'));
        } catch (Exception $e) {
            return false;
        }
    }

    public static function filterUserList($projectParticipantCollectionObject, $request)
    {
        try {
            if ($request->has('search') && !empty($request->search)) {
                $projectParticipantCollectionObject = $projectParticipantCollectionObject->where(function ($query) use ($request) {
                    $query->where('invitee_name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            }

            if ($request->has('access_level') && !empty($request->access_level)) {
                $access_level = null;
                switch ($request->access_level) {
                    case 'team_leader':
                        $access_level = config('constants.project_access_level.team_leader');
                        break;
                    case 'viewer':
                        $access_level = config('constants.project_access_level.viewer');
                        break;
                    case 'editor':
                        $access_level = config('constants.project_access_level.editor');
                        break;
                    default:
                        $access_level = null;
                        break;
                }
                $projectParticipantCollectionObject = $projectParticipantCollectionObject->where('inviter_access_level', $access_level);
            }

            if ($request->has('invite_status') && !empty($request->invite_status)) {
                $invite_status = null;
                switch ($request->invite_status) {
                    case 'invited':
                        $invite_status = config('constants.project_member_management_invite_status.invited');
                        break;
                    case 'accepted':
                        $invite_status = config('constants.project_member_management_invite_status.accepted');
                        break;
                    case 'pending':
                        $invite_status = config('constants.project_member_management_invite_status.pending');
                        break;
                    case 'declined':
                        $invite_status = config('constants.project_member_management_invite_status.declined');
                        break;
                    default:
                        $invite_status = null;
                }
                $projectParticipantCollectionObject = $projectParticipantCollectionObject->where('invite_status', $invite_status);
            }

            if ($request->has('invite_type') && !empty($request->invite_type)) {
                $invite_type = null;
                switch ($request->invite_type) {
                    case 'email':
                        $invite_type = config('constants.project_member_management_invite_type.email');
                        break;
                    case 'network':
                        $invite_type = config('constants.project_member_management_invite_type.network');
                        break;
                    case 'csv':
                        $invite_type = config('constants.project_member_management_invite_type.csv');
                        break;
                    default:
                        $invite_type = null;
                }
                $projectParticipantCollectionObject = $projectParticipantCollectionObject->where('invite_type', $invite_type);
            }

            if ($request->has('email_status') && !empty($request->email_status)) {
                $email_status = null;
                switch ($request->email_status) {
                    case 'scheduled':
                        $email_status = config('constants.project_member_management_email_status.scheduled');
                        break;
                    case 'sent':
                        $email_status = config('constants.project_member_management_email_status.sent');
                        break;
                    case 'fail':
                        $email_status = config('constants.project_member_management_email_status.fail');
                        break;
                    case 'NA':
                        $email_status = config('constants.project_member_management_email_status.na');
                        break;
                    default:
                        $email_status = null;
                }
                $projectParticipantCollectionObject = $projectParticipantCollectionObject->where('email_status', $email_status);
            }

            return $projectParticipantCollectionObject;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getTemplate($requestLang)
    {
        try {
            $module_type = EmailTemplateService::getEmailTemplate(config('constants.email_template_type.invitation'), '5', $requestLang);

            return $module_type;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function fetchDataFromCSV($request)
    {
        try {
            $memberList = [];
            if ($request->hasFile('invite_email')) {
                $file = $request->file('invite_email');
                if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                    $header = fgetcsv($handle, 0, ',');
                    $count_header = count($header);
                    if ($count_header == 3 && in_array('Name', $header) && in_array('Email', $header) && in_array('Access', $header)) {
                        $email_column = array_search('Email', $header);
                        $name_column = array_search('Name', $header);
                        $access_column = array_search('Access', $header);
                        if ($email_column === false || $name_column === false || $access_column === false) {
                            fclose($handle);

                            return false;
                        }
                    } else {
                        fclose($handle);

                        return false;
                    }
                    $memberList = [];
                    while (($csv_get_data = fgetcsv($handle, 1000, ',')) !== false) {
                        $memberList[] = [
                            'invite_type'   => config('constants.project_member_management_invite_type.csv'),
                            'invitee_name'  => $csv_get_data[$name_column],
                            'invitee_email' => $csv_get_data[$email_column],
                            'access_level'  => $csv_get_data[$access_column] ?? null,
                        ];
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
        } catch (Exception $e) {
            return false;
        }
    }

    public static function fetchDataFromEmailArray($request)
    {
        try {
            $participantList = [];
            if (is_array($request->invite_email) && is_array($request->access_level)) {
                foreach ($request->invite_email as $key => $email) {
                    $access_level = $request->access_level[$key] ?? null;

                    $user = UserService::getUserByEmail($email);
                    $name = null;
                    if ($user) {
                        $name = $user->first_name.' '.$user->last_name;
                    }
                    $participantList[] = [
                        'invite_type'   => config('constants.project_member_management_invite_type.email'),
                        'invitee_name'  => $name,
                        'invitee_email' => $email,
                        'access_level'  => $access_level,
                    ];
                }
                if (!empty($participantList)) {
                    return $participantList;
                }

                return false;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addParticipates($projectData, $request, $pariticipateLists)
    {
        try {
            $already_members = [];
            $invalid_emails = [];
            $invited_emails = [];
            $addedMemberResponse = __('responses.create_member_manger_success_prjt');

            DB::beginTransaction();
            foreach ($pariticipateLists as $pariticipateData) {
                if (UtilityHelper::validEmail($pariticipateData['invitee_email'])) {
                    $checkExistenceEntry = ProjectMemberManagement::where(['project_id' => $projectData->id, 'email' => $pariticipateData['invitee_email']])->exists();
                    if ($checkExistenceEntry == false) {
                        $invite_status = config('constants.project_member_management_invite_status.invited');
                        $email_status = config('constants.project_member_management_email_status.scheduled');

                        switch ($pariticipateData['access_level']) {
                            case 'editor':
                                $access_level = config('constants.project_access_level.editor');
                                break;
                            case 'viewer':
                                $access_level = config('constants.project_access_level.viewer');
                                break;
                            default:
                                $access_level = config('constants.project_access_level.viewer');
                                break;
                        }

                        $subject = $request->subject_line;
                        $emailBody = $request->email_body;
                        $module_type = config('constants.member_management_component_type.project');
                        $user_name = UserService::joinName(auth()->user()->first_name, auth()->user()->last_name);

                        if (empty($request->subject_line) || empty($request->email_body)) {
                            $getTemplate = EmailTemplateService::getEmailTemplate(config('constants.email_template_type.invitation'), $module_type, $request->language);
                            if ($getTemplate) {
                                $getTemplate->body_content = str_replace('user_name', $user_name, str_replace('component_title', $projectData->title, $getTemplate->body_content));

                                if (empty($request->subject_line)) {
                                    $subject = $getTemplate->subject;
                                }
                                if (empty($request->email_body)) {
                                    $emailBody = $getTemplate->body_content;
                                }
                            }
                        }

                        // feeding in project member management table
                        self::feedParticipatesData($projectData->id, auth()->user()->id, $pariticipateData['invitee_email'], $pariticipateData['invite_type'], $invite_status, $email_status, $access_level);

                        $invitee_name = $pariticipateData['invitee_name'] != null ? $pariticipateData['invitee_name'] : 'Solver';
                        $email_detail = ['invitee_name' => $invitee_name, 'subject' => $subject, 'body' => $emailBody, 'slug' => config('site-settings.frontend_site_url')];
                        Notification::route('mail', $pariticipateData['invitee_email'])->notify(new InviteMemberNotification($email_detail));
                        $invited_emails[] = $pariticipateData['invitee_email'];
                    } else {
                        $already_members[] = $pariticipateData['invitee_email'];
                    }
                } else {
                    $invalid_emails[] = $pariticipateData['invitee_email'];
                }
            }
            DB::commit();
            if (count($invalid_emails) > 0 || count($already_members) > 0) {
                if (count($invited_emails) < 1) {
                    $addedMemberResponse = __('responses.create_member_manger_error_prjt');
                } else {
                    $addedMemberResponse = __('responses.create_member_manger_error_certain');
                }
            } elseif (count($invited_emails) > 0) {
                $addedMemberResponse = $addedMemberResponse;
            }
            $data = [
                'invalid_emails'            => $invalid_emails,
                'invited_emails'            => $invited_emails,
                'already_members'           => $already_members,
                'add_participant_response'  => $addedMemberResponse,
            ];

            return $data;
        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public static function feedParticipatesData($projectDataId, $inviterId, $inviteeEmail, $inviteType, $inviteStatus, $emailStatus, $accessLevel)
    {
        try {
            $participatesData = ProjectMemberManagement::create([
                'uuid'                      => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                'project_id'                => $projectDataId,
                'inviter_id'                => $inviterId,
                'invitee_id'                => null,
                'email'                     => $inviteeEmail,
                'invite_type'               => $inviteType,
                'invite_status'             => $inviteStatus,
                'email_status'              => $emailStatus,
                'inviter_access_level'      => $accessLevel,
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkProjectJoinUnjoinStatus($request, $projectData)
    {
        try {
            $projectMemberData = ProjectMemberManagement::whereIn('email', $request->email)->where(['project_id' => $projectData->id, 'invite_status' => '2'])->get();
            if ($projectMemberData) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function acceptOrRejectProjectJoinRequest($request, $projectData, $action)
    {
        try {
            switch ($action) {
                case 'accept':
                    $invite_status = config('constants.project_member_management_invite_status.accepted');
                    break;
                case 'decline':
                    $invite_status = config('constants.project_member_management_invite_status.declined');
                    break;
            }

            $projectMemberData = ProjectMemberManagement::whereIn('email', $request->email)->where(['project_id' => $projectData->id, 'invite_status' => '2'])->get();
            foreach ($projectMemberData as $projectMember) {
                $projectMember->invite_status = $invite_status;
                $projectMember->inviter_id = auth()->user()->id;
                $projectMember->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkCurrentProjectRole($projectId, $uuid, $role)
    {
        try {
            switch ($role) {
                case 'team_leader':
                    $currentRole = '2';
                    break;
                case 'editor';
                    $currentRole = '1';
                    break;
                case 'viewer';
                    $currentRole = '0';
                    break;
                default:
                    $currentRole = '0';
                    break;
            }

            $checkCurrentRole = ProjectMemberManagement::where(['uuid' => $uuid, 'project_id' => $projectId, 'inviter_access_level' => $currentRole])->exists();
            if (!$checkCurrentRole) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateProjectRole($projectId, $uuid, $role)
    {
        try {
            switch ($role) {
                case 'team_leader':
                    $newtRole = '2';
                    break;
                case 'editor';
                    $newtRole = '1';
                    break;
                case 'viewer';
                    $newtRole = '0';
                    break;
                default:
                    $newtRole = '0';
                    break;
            }

            $updateNewRole = ProjectMemberManagement::where(['uuid' => $uuid, 'project_id' => $projectId])->update(['inviter_access_level' => $newtRole]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function deleteParticipates($projectData, $request)
    {
        try {
            $projectMemberData = ProjectMemberManagement::whereIn('email', $request->email)->where(['project_id' => $projectData->id])->delete();
            if ($projectMemberData) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
