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
            if($request->hasFile('invite_email')){
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
            if(is_array($request->invite_email)){
                foreach ($request->invite_email as $email) {
                    $user = UserService::getUserByEmail($email);
                    $name=null;
                    if($user){
                        $name = $user->first_name.' '.$user->last_name;
                    }
                    $memberList[] = [
                        "type" => config('constants.member_management_type.invite'),
                        "invite_type" => config('constants.member_management_invite_type.email'),
                        "invitee_name" => $name,
                        "invitee_email" => $email,
                    ];
                }
                if(!empty($memberList)){
                    return $memberList;
                }
                return false;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function addMembers($componentCollectionObject,$component, $request, $memberList){
        try{
            $already_members = [];
            $invalid_emails = [];
            $invited_emails = [];
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    break;
                default:
                    $module_type = null;
                    break;
            }
            $auto_invite = config('constants.member_management_auto_invite.no');

            switch ($request->auto_invite) {
                case ('Yes' || 'YES' || 'yes'):
                    $auto_invite = config('constants.member_management_auto_invite.yes');
                    break;
                case ('No' || 'NO' || 'no'):
                    $auto_invite = config('constants.member_management_auto_invite.no');
                    break;
                case ('Na' || 'NA' || 'na') :
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

            return false;
        }
    }

    public function getComponentBasedUser($componentCollectionObject,$component,$slug,$request){
        try{
            switch ($component) {
                case 'organization':
                    $module_type = config('constants.member_management_component_type.organization');
                    break;
                default:
                    $module_type = null;
                    break;
            }
        }
        catch (\Exception $e){

        }
    }
}
