<?php

namespace App\Repositories\Api\Manage\ResourceModule;

interface ResourceModuleInterface
{
    public function getResourceModuleList($request, $organization);

    public function createResourceModule($request, $upload_media);

    public function uploadResourceModuleMedia($cover_image);

    public function getResourceModuleBasedOnSlug($slug);

    public function checkSlug($slug);

    public function checkName($title);

    public function delete($slug, $resource_module_id);

    public function addLinks($request, $resource_module_id);

    public function addEmbeddedMedia($request, $resource_module_id);

    public function updateResourceModule($slug, $request, $upload_cover_image);

    public function fileUpload($request, $resource_module_id, $type);

    public function deleteMedia($request, $resource_module_id, $type);
}
