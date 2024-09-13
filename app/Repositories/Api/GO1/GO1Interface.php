<?php

namespace App\Repositories\Api\GO1;

interface GO1Interface
{
    public function getCourseLists();

    public function createResourceModule($request);

    public function listFilters($type);

    public function getResourceModuleBySlug($slug);

    public function playCourse($go1CourseId);

    public function webhook($payload);

    public function canPlayGO1Resoruces();
}
