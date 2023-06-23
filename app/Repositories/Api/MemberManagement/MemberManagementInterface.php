<?php

namespace App\Repositories\Api\MemberManagement;

interface MemberManagementInterface
{
    public function getMembers($component, $slug, $request);

    public function addMembers($componentCollectionObject,$component, $request);

    public function deleteMembers($component, $slug, $request);

    public function downloadSample();

    public function getRoles($role_type);
}
