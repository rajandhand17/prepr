<?php

namespace App\Services;

use App\Models\OrganizationAddress;
use DB;
use App\Models\MemberManagement;
use App\Models\User;
class MemberManagementService
{
    public function getRecordsFromCsv($request)
    {
        try {
            $invite_type = '3';
            $csv_email_data = [];
            /** checking extension of file  */
            if (in_array($request->file('invite_email')->getClientMimeType(), ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv'])) {
                if (($handle = fopen($request->invite_email, 'r')) !== false) {
                    $header = fgetcsv($handle, 0, ',');
                    $count_header = count($header);
                    /**Checking columns names in csv  */
                    if ($count_header == 2 && in_array('Name', $header) && in_array('Email', $header)) {
                        /**checking place of email column one or two */
                        if ($header[0] == 'Email') {
                            $email_column = 0;
                        } else {
                            $email_column = 1;
                        }
                    }else{
                        return false;
                    }
                    /**getting data from csv and convert in array */
                    while (($csv_get_data = fgetcsv($handle, 1000, ',')) !== false) {
                        $invitee[] = $csv_get_data[$email_column];
                    }
                    fclose($handle);
                    return $invitee;
                }

                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function insert($invitee, $request)
    {
        try {
            if (!MemberManagement::where(['module_id' => (int) $request->module_id, 'role' => $request->role, 'email' => trim($invitee)])->exists()) {
                $member_management_data = new MemberManagement();
                $member_management_data->type = $request->type;
                $member_management_data->invite_type = $request->invite_type;
                $member_management_data->role = $request->role;
                $member_management_data->email = trim($invitee);
                $member_management_data->module_id = (int) $request->module_id;
                $member_management_data->inviter_id = (int) $request->inviter_id;
                $member_management_data->subject_line = $request->subject_line;
                $member_management_data->email_body = $request->email_body;
                $member_management_data->invite_status = $request->invite_status;
                $member_management_data->email_status = '0';
                $member_management_data->email_resend_count = '0';
                if ($member_management_data->save()) {
                    return true;
                }

                return false;
            } else {
                return 'already_exists';
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function checkEmail($invite_member){
        $user=User::select('email')->where(['id' => (int) $invite_member])->first();
        if($user){
        
        }else{
        return null;
        }
    }
}