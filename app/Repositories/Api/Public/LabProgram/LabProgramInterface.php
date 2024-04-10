<?php

namespace App\Repositories\Api\Public\LabProgram;

interface LabProgramInterface
{
    public function getList($request);

    public function getLabProgramBasedOnSlug($slug);

    public function captureSocialActivity($id, $column, $value);

    public function checkSocialActivity($lab_id, $column, $action);
}
