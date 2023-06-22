<?php

namespace App\Repositories\Api\MemberManagement;

interface MemberManagementInterface
{
    public function index($component, $slug, $request,$memberManagementService);
    public function delete($component, $slug, $request,$memberManagementService);
    public function create($component, $slug, $request,$memberManagementService);
}
