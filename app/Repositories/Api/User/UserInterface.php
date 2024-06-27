<?php

namespace App\Repositories\Api\User;

interface UserInterface
{
    public function getUsers($request);

    public function organizationListing($request);

    public function setOrganizationPreference($organizationId);

    public function userOnboarding();

    public function organizationOnboarding($organizationId, $request);
}
