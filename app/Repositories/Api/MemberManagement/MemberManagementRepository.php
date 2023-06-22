<?php

namespace App\Repositories\Api\MemberManagement;

use App\Models\MemberManagement;
use App\Services\MemberManagementService;
use Response;

class MemberManagementRepository implements MemberManagementInterface
{
    private $member_mangement;

    public function __construct(MemberManagementService $memberManagementService)
    {
        $this->member_mangement = $memberManagementService;
    }

    public function getMembers($component, $slug, $request)
    {
        try {
            return $this->member_mangement->index($component, $slug, $request);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteMembers($component, $slug, $request)
    {
        try {
            return $this->member_mangement->delete($request);
        }catch (\Exception $e) {
            return false;
        }
    }

     public function addMembers($component, $slug, $request)
    {
        try {
            $invalid_email_data = [];
            $invalid_users = [];
            $inserted_emails = [];
            $not_inserted_emails = [];
            $already_exists_emails = [];
            $request->type = '0';
            $request->invite_status = '0';
            $invitee = [];
            if ($request->invite_type != 'csv') {
                if (gettype($request->invite_email) != 'string') {
                    return false;
                }
                $invitee = explode(',', $request->invite_email);

                if (!is_array($invitee)) {
                    return false;
                }
                if ($request->invite_type == 'email') {
                    $request->invite_type = '0';
                }
                if ($request->invite_type == 'network') {
                    $request->invite_type = '1';
                }
            }
            if ($request->invite_type == 'csv') {
                $request['invite_type'] = '3';
                 /**get records from csv */
                $invitee = $this->member_mangement->getRecordsFromCsv($request);
                if (!$invitee) {
                    return false;
                }
            }
             /**convert string to array */
            foreach ($invitee as $invite_member) {
                 /**in case invite type email */
                if ($request->invite_type == '1'){
                    $user_data =$this->member_mangement->checkEmail($invite_member);
                    if ($user_data == null){
                        $invalid_users[] = $invite_member;
                        continue;
                    }
                    $invite_member = $user_data->email;
                }
                if (filter_var(trim($invite_member), FILTER_VALIDATE_EMAIL)) {
                    $result =  $this->member_mangement->insert($invite_member, $request);
                    if ($result === true) {
                    $inserted_emails[] = $invite_member;
                    }
                    if ($result === false) {
                    $not_inserted_emails[] = $invite_member;
                    }
                    if ($result === 'already_exists') {
                    $already_exists_emails[] = $invite_member;
                    }
                } else {
                    $invalid_email_data[] = $invite_member;
                }
            }
            $invalid_users = implode(',', $invalid_users);
            $response = new \stdClass();
            if (!empty($inserted_emails) || !empty($already_exists_emails)) {
                $response->status = true;
                $response->already_exists_emails = $already_exists_emails;
                $response->inserted_emails = $inserted_emails;
                $response->invalid_email_data = $invalid_email_data;
                if ($request->invite_type == '1') {
                    $response->invalid_users = $invalid_users;
                }
                return $response;
            }
            $response->status = false;
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return MemberManagementService
     */
    public function downloadSample()
    {
        try{
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=member-management-sample.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $columns  = ['Name', 'Email'];
            $callback = function() use ($columns)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                fclose($file);
            };
            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return false;
        }
    }
}
