<?php

namespace App\Repositories\Api\Manage\LabProgram;

interface LabProgramInterface
{
    public function getLabProgramCountBasedOnOrganization($organizationId);

    public function getLabProgramList($request, $organization);

    public function getLabProgramBasedOnSlug($slug);

    public function uploadLabProgramMedia($slug);

    public function createLabProgram($request, $upload_media, $upload_achievement_image, $organizationId);

    public function updateLabProgram($slug, $request, $upload_media, $upload_achievement_image, $organizationId);

    public function checkSlug($slug);

    public function delete($slug);

    public function checkNameExistsOrNot($title);

    public function getLabProgramListName($request, $organization);
}
