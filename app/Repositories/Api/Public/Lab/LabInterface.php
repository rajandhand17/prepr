<?php

namespace App\Repositories\Api\Public\Lab;

interface LabInterface
{
    public function getList($request);
    public function getLabBasedOnSlug($slug);
    public function socialActivity($id,$column,$value);
    public function checkSocialActivity($lab_id,$action);

}
