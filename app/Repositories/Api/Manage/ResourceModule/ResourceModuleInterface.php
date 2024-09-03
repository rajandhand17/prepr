<?php

namespace App\Repositories\Api\Manage\ResourceModule;

interface ResourceModuleInterface
{
    public function getResourceModuleCountBasedOnOrganization($organizationId);

    public function getResourceModuleList($request, $organization);

    public function createResourceModule($request, $upload_cover_image, $organizationId);

    public function createResourceModuleUsingAI($request, $upload_cover_image, $organizationId);

    public function createResourceModuleDetailsAI($request, $resource_module_id);

    public function createResourceModuleUsingAIPreview($request);

    public function uploadResourceModuleCoverImage($cover_image);

    public function getResourceModuleBasedOnSlug($slug);

    public function deleteResourceModule($slug, $resource_module_id);

    public function checkName($title);

    public function updateResourceModule($slug, $request, $upload_cover_image, $organizationId);

    public function fileUpload($request, $resource_module_id);

    public function deleteResourceModuleMedia($request, $resource_module_id);

    public function addLinks($request, $resource_module_id);

    public function addEmbeddedMedia($request, $resource_module_id);

    public function getListName($request, $organization);
}
