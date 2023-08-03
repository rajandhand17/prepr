<?php

namespace App\Repositories\Api\Public\Lab;

interface LabInterface
{
    public function getLabList($request);

    public function labSocialActivitiesService($id, $column, $value);
}
