<?php

namespace App\Repositories\Api\Organization;

interface OrganizationInterface
{
    public function view($slug, $language);

    public function create($request);

    public function update($slug, $language);

    public function delete($language, $slug);

    public function list($language);

    public function organizationMemberView($id, $language);
}
