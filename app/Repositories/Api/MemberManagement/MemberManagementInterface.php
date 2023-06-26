<?php

namespace App\Repositories\Api\MemberManagement;

interface MemberManagementInterface
{
    public function getMembers($componentCollectionObject, $component);

    public function addMembers($componentCollectionObject, $component, $request);

    public function deleteMembers($checkComponentBasedOnSlug,$request);

    public function downloadSample();

    public function getRoles($role_type);
}
