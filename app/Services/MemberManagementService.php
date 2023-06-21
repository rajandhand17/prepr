<?php

namespace App\Services;

use App\Models\OrganizationAddress;
use DB;
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
                    } else {
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
}