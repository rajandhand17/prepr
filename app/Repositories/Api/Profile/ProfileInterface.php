<?php

namespace App\Repositories\Api\Profile;

interface ProfileInterface
{
    public function getUserByUsername($user_name);
    public function createPersonalDetail($request);

    public function addPatent($request);

    public function addEducation($request);

    public function addExperience($request);
}
