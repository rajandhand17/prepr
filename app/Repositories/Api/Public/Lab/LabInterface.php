<?php

namespace App\Repositories\Api\Public\Lab;

use App\Models\Lab;
use App\Models\User;

interface LabInterface
{
    public function getList($request);

    public function getLabBasedOnSlug($slug);

    public function captureSocialActivity($id, $column, $value);

    public function checkSocialActivity($lab_id, $column, $action);

    public function checkJoinedOrNot($lab, $moduleType);

    public function joinLab($lab, $component, $request, $memberList);

    public function unJoinLab($lab, $component, $request);

    public function canJoinLiveEvent(Lab $lab, User $user);

    public function sendLiveEventInvitationLinkToMembers(Lab $lab);

    public function liveEventDetails(Lab $lab);
}
