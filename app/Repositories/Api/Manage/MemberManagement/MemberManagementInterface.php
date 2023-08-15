<?php

namespace App\Repositories\Api\Manage\MemberManagement;

interface MemberManagementInterface
{
    public function getMembers($componentCollectionObject, $component, $request);

    public function getTemplate($request, $component);

    public function downloadSample();

    public function getRoles($role_type);

    public function addMembers($componentCollectionObject, $component, $request);

    public function deleteMembers($checkComponentBasedOnSlug, $component, $request);

    public function checkLabJoinUnjoinStatus($request, $checkComponentBasedOnSlug, $component);

    public function acceptOrRejectLabJoinRequest($request, $checkComponentBasedOnSlug, $component, $action);

    public function changeRole($request, $component);
}
