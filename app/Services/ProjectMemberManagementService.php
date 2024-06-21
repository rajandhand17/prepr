<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\ProjectAccessLevel;
use App\Models\ProjectMemberManagement;
use App\Notifications\InviteMemberNotification;
use App\Services\Manage\EmailTemplateService;
use Exception;
use HiFolks\RandoPhp\Randomize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ProjectMemberManagementService
{
    public function getRoles()
    {
        try {
            $getRoles = ProjectAccessLevel::select('display_name')->get();

            return $getRoles;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProjectBasedParticipants($projectData, $request)
    {
        try {
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

            if ($request->has('role') && !empty($request->role)) {
                $access_level = null;
                switch ($request->role) {
                    case 'Team Leader':
                        $access_level = config('constants.project_access_level.team_leader');
                        break;
                    case 'Viewer':
                        $access_level = config('constants.project_access_level.viewer');
                        break;
                    case 'Editor':
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
            if (is_array($request->invite_email) && is_array($request->role)) {
                foreach ($request->invite_email as $key => $email) {
                    $role = $request->role[$key] ?? null;

                    $user = UserService::getUserByEmail($email);
                    $name = ($user != false) ? $user->full_name : ($request->name[$key] ?? null);
                    $participantList[] = [
                        'invite_type'   => config('constants.project_member_management_invite_type.email'),
                        'invitee_name'  => $name,
                        'invitee_email' => $email,
                        'access_level'  => $role,
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

                        switch (strtolower($pariticipateData['access_level'])) {
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
                        self::feedParticipatesData($projectData->id, auth()->user()->id, $pariticipateData['invitee_email'], $pariticipateData['invitee_name'], $pariticipateData['invite_type'], $invite_status, $email_status, $access_level, $subject, $emailBody);

                        $invitee_name = $pariticipateData['invitee_name'] != null ? $pariticipateData['invitee_name'] : 'Solver';
                        $email_detail = ['invitee_email' => $pariticipateData['invitee_email'], 'invitee_name' => $invitee_name, 'subject' => $subject, 'body' => $emailBody, 'slug' => config('site-settings.frontend_site_url')];
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

    public static function feedParticipatesData($projectDataId, $inviterId, $inviteeEmail, $invitee_name, $inviteType, $inviteStatus, $emailStatus, $accessLevel, $subject, $emailBody)
    {
        try {
            $participatesData = ProjectMemberManagement::create([
                'uuid'                      => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                'project_id'                => $projectDataId,
                'inviter_id'                => $inviterId,
                'email'                     => $inviteeEmail,
                'invitee_name'              => $invitee_name,
                'invite_type'               => $inviteType,
                'invite_status'             => $inviteStatus,
                'email_status'              => $emailStatus,
                'inviter_access_level'      => $accessLevel,
                'subject_line'              => $subject,
                'email_body'                => $emailBody,
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkProjectJoinUnjoinStatus($userEmail, $projectData)
    {
        try {
            $projectMemberData = ProjectMemberManagement::where(['project_id' => $projectData->id, 'email' => $userEmail, 'invite_status' => '2', 'invite_type' => '3'])->first();
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

            $projectMemberData = ProjectMemberManagement::where(['project_id' => $projectData->id, 'email' => $request->email, 'invite_status' => '2', 'invite_type' => '3'])->get();
            foreach ($projectMemberData as $projectMember) {
                $user = UserService::getUserByEmail($request->email);
                $projectMember->invite_status = $invite_status;
                $projectMember->inviter_id = auth()->user()->id;
                $projectMember->invitee_name = $user->full_name;
                $projectMember->save();
                $user = UserService::getUserByEmail($request->email);
                $activity = auth()->user()->full_name.' '.__('responses.project_updated_member_activity').' '.$user->full_name;
                ProjectHistoryService::storeHistory($projectData->id, auth()->user()->id, $activity);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkParticipantsUUID($projectId, $uuid)
    {
        try {
            $checkParticipantsUUID = ProjectMemberManagement::where(['project_id' => $projectId, 'uuid' => $uuid])->exists();
            if ($checkParticipantsUUID) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkCurrentProjectRole($projectId, $uuid, $role)
    {
        try {
            switch ($role) {
                case 'Team Leader':
                    $currentRole = '2';
                    break;
                case 'Editor':
                    $currentRole = '1';
                    break;
                case 'Viewer':
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
                case 'Team Leader':
                    $newtRole = '2';
                    break;
                case 'Editor':
                    $newtRole = '1';
                    break;
                case 'Viewer':
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

    public static function getAcceptedInvitesProjectIds($userData)
    {
        try {
            $getMyProjectIds = ProjectService::getMyProjectIds($userData->id);
            $getAcceptedInvitesProjectIds = ProjectMemberManagement::where(['email' => $userData->email, 'invite_status' => '1'])->whereNotIn('project_id', $getMyProjectIds)->pluck('project_id');

            return $getAcceptedInvitesProjectIds;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getPendingInvitesProjectIds($userData)
    {
        try {
            $getMyProjectIds = ProjectService::getMyProjectIds($userData->id);
            $getAcceptedInvitesProjectIds = ProjectMemberManagement::where(['email' => $userData->email, 'invite_status' => '0'])->where('invite_type', '<>', '3')->whereNotIn('project_id', $getMyProjectIds)->pluck('project_id');
            return $getAcceptedInvitesProjectIds;
        } catch (Exception $e) {
            return false;
        }
    }

    public function fetchAcceptedMemberIds($projectId)
    {
        try {
            $getUserIdsBasedOnEmail = [];

            $fetchAcceptedMemberIds = ProjectMemberManagement::where(['project_id' => $projectId, 'invite_status' => '1'])->pluck('email');
            if ($fetchAcceptedMemberIds && count($fetchAcceptedMemberIds) > 0) {
                $getUserIdsBasedOnEmail = UserService::getUserIdsByEmail($fetchAcceptedMemberIds);
            }

            return $getUserIdsBasedOnEmail;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkParticipantProjectJoinUnjoinStatus($userEmail, $projectData)
    {
        try {
            $projectMemberData = ProjectMemberManagement::where(['project_id' => $projectData->id, 'email' => $userEmail, 'invite_status' => '0'])->where('invite_type', '<>', '3')->first();
            if ($projectMemberData) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function participantAcceptOrRejectJoinRequest($userEmail, $projectData, $action)
    {
        try {
            switch ($action) {
                case 'accept':
                    $invite_status = config('constants.member_management_invite_status.accepted');
                    break;
                case 'decline':
                    $invite_status = config('constants.member_management_invite_status.declined');
                    break;
            }
            $project_member = ProjectMemberManagement::where(['email' => $userEmail, 'project_id' => $projectData->id, 'invite_status' => '0'])->where('invite_type', '<>', '3')->first();
            if ($project_member) {
                $user = UserService::getUserByEmail($userEmail);
                $activity = auth()->user()->full_name.' '.__('responses.project_updated_member_activity').' '.$user->full_name;
                ProjectHistoryService::storeHistory($projectData->id, auth()->user()->id, $activity);
                $project_member->update(['inviter_id' => auth()->user()->id, 'invite_status' => $invite_status, 'invitee_name' => $user->full_name]);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function checkProjectJoinedStatus($projectId, $userEmail)
    {
        try {
            $checkProjectJoinedStatus = ProjectMemberManagement::where(['project_id' => $projectId, 'email' => $userEmail])->first();
            if ($checkProjectJoinedStatus) {
                return $checkProjectJoinedStatus;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function joinProject($projectId, $userEmail)
    {
        try {
            $getUser = UserService::getUserByEmail($userEmail);
            $joinProject = ProjectMemberManagement::create([
                'uuid'                      => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                'project_id'                => $projectId,
                'inviter_id'                => $getUser->id,
                'email'                     => $getUser->email,
                'invitee_name'              => $getUser->full_name,
                'invite_type'               => '3',
                'invite_status'             => '2',
                'email_status'              => '1',
                'inviter_access_level'      => '0',
                'subject_line'              => null,
                'email_body'                => null,
            ]);
            if ($joinProject) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function unJoinProject($projectId, $userEmail)
    {
        try {
            $unJoinProject = ProjectMemberManagement::where(['project_id' => $projectId, 'email' => $userEmail])->delete();
            if ($unJoinProject) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getMatchedTeams()
    {
        try {
            $getMatchedTeams = ProjectMemberManagement::where([
                'invite_status'=> '1',
                'inviter_id'   => auth()->user()->id,
            ])->pluck('project_id');

            return $getMatchedTeams;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getProjectMemberManagementEmails($projectId)
    {
        try {
            $memberManagement = ProjectMemberManagement::where([
                'project_id'    => $projectId,
                'invite_status' => config('constants.member_management_invite_status.accepted'),
            ])->pluck('email');

            return $memberManagement;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getAllRequestsData($requestStatus)
    {
        try {
            $memberManagement = ProjectMemberManagement::select();
            if ($requestStatus == 'request_sent') {
                $memberManagement = $memberManagement->where(['email'=>auth()->user()->email, 'invite_status'=>'0']);
            } else {
                $memberManagement = $memberManagement->where('email', '!=', auth()->user()->email);
            }

            return $memberManagement->pluck('project_id');
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function sendRequest($projectId)
    {
        try {
            $getUser = auth()->user();
            $joinProject = ProjectMemberManagement::create([
                'uuid'                      => Randomize::chars(10)->alphanumeric()->unique()->generate(),
                'project_id'                => $projectId,
                'inviter_id'                => $getUser->id,
                'email'                     => $getUser->email,
                'invitee_name'              => $getUser->full_name,
                'invite_type'               => '3',
                'invite_status'             => '2',
                'email_status'              => '1',
                'inviter_access_level'      => '0',
                'subject_line'              => null,
                'email_body'                => null,
            ]);
            if ($joinProject) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function checkRequestExistsOrNotExists($projectId)
    {
        try {
            $checkExistsOrNot = ProjectMemberManagement::where([
                'email'     => auth()->user()->email,
                'project_id'=> $projectId,
            ])->first();
            if ($checkExistsOrNot) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
