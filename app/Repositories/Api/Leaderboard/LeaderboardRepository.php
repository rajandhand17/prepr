<?php

namespace App\Repositories\Api\Leaderboard;

use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\ProjectMemberManagementService;
use App\Services\UserService;

class LeaderboardRepository implements LeaderboardInterface
{
    private $userService;

    private $labService;

    private $memberManagerService;

    public function __construct(UserService $userService, LabService $labService, MemberManagementService $memberManagerService)
    {
        $this->userService = $userService;
        $this->labService = $labService;
        $this->memberManagerService = $memberManagerService;
    }

    public function getLeaderBoardList($request)
    {
        try {
            $membersEmails=[];
            if (
                ($request->has('organization_id') && !empty($request->organization_id)) ||
                ($request->has('lab_id') && !empty($request->lab_id)) ||
                ($request->has('challenge_id') && !empty($request->challenge_id))
            ) {
                $membersEmails = array_unique(array_merge($membersEmails,MemberManagementService::getMembersManagerUsersBasedOnFilter($request)));
            }
            if($request->has('project_id') && !empty($request->project_id)) {
                $membersEmails = array_unique(array_merge($membersEmails,ProjectMemberManagementService::getProjectMemberManagementEmails($request->project_id)));
            }
          $user = $this->userService->getLeaderBoardList($request,$membersEmails);
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getComponentsMembers($slug, $component, $request)
    {
        try {
            switch ($component) {
                case 'lab':
                    $componentId = $this->labService->checkSlug($slug)->id;
                    $userEmails = $this->memberManagerService->getMembersBasedOnComponentId($component, $componentId);
                    break;
                default:
                    return false;
            }

            return $this->userService->getComponentBasedUsers($userEmails, $request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
