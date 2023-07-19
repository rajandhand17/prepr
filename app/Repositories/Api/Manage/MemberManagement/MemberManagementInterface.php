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

    public function changeRole($request, $component);
}
