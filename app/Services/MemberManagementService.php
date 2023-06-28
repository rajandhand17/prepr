<?php

namespace App\Services;

use App\Helpers\UtilityHelper;
use App\Models\MemberManagement;
use DB;

class MemberManagementService
{
    public function getRecordsFromCsv($request)
    {
        try {
            $memberList = [];
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
                        $memberList[] = [
                            'type'          => config('constants.member_management_type.invite'),
                            'invite_type'   => config('constants.member_management_invite_type.csv'),
                            'invitee_name'  => $csv_get_data[$name_column],
                            'invitee_email' => $csv_get_data[$email_column],
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
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getRecordsFromEmailArray($request)
    {
        try {
            $memberList = [];
            if (is_array($request->invite_email)) {
                foreach ($request->invite_email as $email) {
                    $user = UserService::getUserByEmail($email);
                    $name = null;
                    if ($user) {
                        $name = $user->first_name.' '.$user->last_name;
                    }
                    $memberList[] = [
                        'type'          => config('constants.member_management_type.invite'),
                        'invite_type'   => config('constants.member_management_invite_type.email'),
                        'invitee_name'  => $name,
                        'invitee_email' => $email,
                    ];
                }
                if (!empty($memberList)) {
                    return $memberList;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addMembers($componentCollectionObject, $component, $request, $memberList)
    {
        try {
            $already_members = [];
            $invalid_emails = [];
            $invited_emails = [];
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    break;
                case 'lab':
                    $module_type=config('constants.member_management_component_type.lab');
                   break;
                default:
                    $module_type = null;
                    break;
            }
            $auto_invite = config('constants.member_management_auto_invite.no');

            switch ($request->auto_invite) {
                case 'Yes' || 'YES' || 'yes':
                    $auto_invite = config('constants.member_management_auto_invite.yes');
                    break;
                case 'No' || 'NO' || 'no':
                    $auto_invite = config('constants.member_management_auto_invite.no');
                    break;
                case 'Na' || 'NA' || 'na' :
                    $auto_invite = config('constants.member_management_auto_invite.na');
                    break;
                default:
                    $auto_invite = config('constants.member_management_auto_invite.no');
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
                            $invite_status = config('constants.member_management_invite_status.invited');
                            if ($auto_invite == 0) {
                                $invite_status = config('constants.member_management_invite_status.invited');
                            }

                            if ($auto_invite == 1) {
                                $invite_status = config('constants.member_management_invite_status.accepted');
                            }

                            if ($auto_invite == 2) {
                                if ($member['type'] == '1') {
                                    $invite_status = config('constants.member_management_invite_status.pending');
                                } elseif ($member['type'] == '2') {
                                    $invite_status = config('constants.member_management_invite_status.auto_created');
                                }
                            }

                            MemberManagement::create([
                                'type'          => $member['type'],
                                'invite_type'   => $member['invite_type'],
                                'module_id'     => $componentCollectionObject->id,
                                'module_type'   => $module_type,
                                'inviter_id'    => ($member['type'] == 0) ? auth()->user()->id : $componentCollectionObject->user_id,
                                'role'          => $request->role,
                                'email'         => $member['invitee_email'],
                                'auto_invite'   => $auto_invite,
                                'invite_status' => $member['type'],
                                'invitee_name'  => $member['invitee_name'],
                                'email_status'  => config('constants.member_management_email_status.scheduled'),
                                'subject_line'  => $request->subject_line,
                                'email_body'    => $request->email_body,
                            ]);

                            $invited_emails[] = $member['invitee_email'];
                        } else {
                            $already_members[] = $member['invitee_email'];
                        }
                    } else {
                        $invalid_emails[] = $member['invitee_email'];
                    }
                }
                DB::commit();
                $data = [
                    'invalid_emails'  => $invalid_emails,
                    'invited_emails'  => $invited_emails,
                    'already_members' => $already_members,
                ];

                return $data;
            }
            DB::rollBack();

            return false;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;

            return false;
        }
    }

    public function getComponentBasedUsers($componentCollectionObject, $component, $request)
    {
        try {
            $memberList = [];
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    $memberListCollection = MemberManagement::select(
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
                    )->where([
                        'module_id'   => $componentCollectionObject->id,
                        'module_type' => $module_type,
                    ]);
                    break;
                default:
                    $module_type = null;
                    break;
            }
            $memberList = $this->filterUserList($memberListCollection, $request);

            return $memberList;
        } catch (\Exception $e) {
            return $e;

            return false;
        }
    }

    public function deleteMembers($checkComponentBasedOnSlug, $component, $request)
    {
        try {
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    break;
                default:
                    $module_type = null;
                    break;
            }
            $member_manger = MemberManagement::whereIn('email', $request->email)->where(['module_id'=>$checkComponentBasedOnSlug->id, 'module_type'=>$module_type])->delete();
            if ($member_manger) {
                return true;
            }

            return false;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function filterUserList($componentCollectionObject, $request)
    {
        try {
            if (isset($request->role) && !empty($request->role)) {
                $componentCollectionObject = $componentCollectionObject->where('role', $request->role);
            }
            if (isset($request->invite_status) && !empty($request->invite_status)) {
                switch ($request->invite_status) {
                    case 'accepted':
                        $invite_status = config('constants.member_management_invite_status.accepted');
                        break;
                    case 'pending':
                        $invite_status = config('constants.member_management_invite_status.pending');
                        break;
                    default:
                        $invite_status = config('constants.member_management_invite_status.invited');
                }

                $componentCollectionObject = $componentCollectionObject->where('invite_status', $invite_status);
            }
            if (isset($request->invite_type) && !empty($request->invite_type)) {
                switch ($request->invite_type) {
                    case 'email':
                        $invite_type = config('constants.member_management_invite_type.email');
                        break;
                    case 'network':
                        $invite_type = config('constants.member_management_invite_type.network');
                        break;
                    case 'csv':
                        $invite_type = config('constants.member_management_invite_type.csv');
                        break;
                    default:
                        $invite_type = config('constants.member_management_invite_type.email');
                }
                $componentCollectionObject = $componentCollectionObject->where('invite_type', $invite_type);
            }
            if (isset($request->email_status) && !empty($request->email_status)) {
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
                    default:
                        $email_status = config('constants.member_management_email_status.scheduled');
                }
                $componentCollectionObject = $componentCollectionObject->where('email_status', $email_status);
            }

            return $componentCollectionObject->get();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function changeRoleById($request, $component)
    {
        try {
            DB::beginTransaction();
            $checkMember = MemberManagement::find($request->id);
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
            DB::rollBack();

            return false;
        }
    }
}
