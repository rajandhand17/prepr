<?php

namespace App\Repositories\Api\Leaderboard;

use App\Services\Manage\LabService;
use App\Services\Manage\MemberManagementService;
use App\Services\ProjectMemberManagementService;
use App\Services\UserService;
use Illuminate\Support\Collection;

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
            $membersEmails = new Collection();
            if (
                ($request->has('organization_id') && !empty($request->organization_id)) ||
                ($request->has('lab_id') && !empty($request->lab_id)) ||
                ($request->has('challenge_id') && !empty($request->challenge_id))
            ) {
                $membersEmails = $membersEmails->merge(MemberManagementService::getMembersManagerUsersBasedOnFilter($request));
            }
            if ($request->has('project_id') && !empty($request->project_id)) {
                $membersEmails = $membersEmails->merge(ProjectMemberManagementService::getProjectMemberManagementEmails($request->project_id));
            }
            $uniqueEmails = $membersEmails->unique();
            $user = $this->userService->getLeaderBoardList($request, $uniqueEmails);
            return $user;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getComponentsMembers($componentId, $component, $request)
    {
        try {
            $userEmails = $this->memberManagerService->getMembersBasedOnComponentId($component, $componentId);

            return $this->userService->getComponentBasedUsers($userEmails, $request);
        } catch (\Exception $e) {
            return false;
        }
    }
}
