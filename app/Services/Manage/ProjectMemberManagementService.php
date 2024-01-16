<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ProjectMemberManagement;
use Exception;
use HiFolks\RandoPhp\Randomize;

class ProjectMemberManagementService
{
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
                            'invite_type' => config('constants.project_member_management_invite_type.csv'),
                            'invitee_name' => $csv_get_data[$name_column],
                            'invitee_email' => $csv_get_data[$email_column],
                            'access_level' => $csv_get_data[$access_column] ?? null,
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

    public function addParticipates($projectData, $request, $pariticipateLists)
    {
        try {
            $already_members = [];
            $invalid_emails = [];
            $invited_emails = [];

            foreach ($pariticipateLists as $pariticipateData) {
                if (UtilityHelper::validEmail($pariticipateData['invitee_email'])) {
                    $checkExistenceEntry = ProjectMemberManagement::where(['project_id' => $projectData->id, 'email' => $pariticipateData['invitee_email']])->exists();
                    if ($checkExistenceEntry == false) {
                        $invite_status = config('constants.project_member_management_invite_status.invited');
                        $email_status = config('constants.project_member_management_email_status.scheduled');
                        
                    } else {
                        $already_members[] = $pariticipateData['invitee_email'];
                    }
                }
                dd($pariticipateData);
            }
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }
}
