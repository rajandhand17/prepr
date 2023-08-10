<?php

namespace App\Repositories\Api\Public\Lab;

interface LabInterface
{
    public function getList($request);

    public function getLabBasedOnSlug($slug);

    public function captureSocialActivity($id, $column, $value);

    public function checkSocialActivity($lab_id, $column, $action);

    public function checkJoinedOrNot($lab, $component);

    public function joinLab($lab, $component, $request, $memberList);

    public function unJoinLab($lab, $component, $request);
}
