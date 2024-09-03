<?php

namespace App\Repositories\Api\Manage\LabAddress;

interface LabAddressInterface
{
    public function store($component, $request, $upload_profile_image);
}
