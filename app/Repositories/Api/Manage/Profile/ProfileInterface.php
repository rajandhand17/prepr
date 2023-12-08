<?php

namespace App\Repositories\Api\Manage\Profile;

interface ProfileInterface
{
    public function getProfileBasedOnUserName($user_name);

    public function addPersonalDetail($request);

<<<<<<< HEAD
    public function addEducation($request);

=======
    public function addExperience($request);
>>>>>>> feature/LLAI-177-api-development---profile
}
