<?php

namespace App\Repositories\Api\Manage\ResourceModule;

interface ResourceModuleInterface
{
    public function getResourceModuleList($request);

    public function createResourceModule($request,$upload_media);

    public function getResourceModuleBasedOnSlug($slug);

    public function checkSlug($slug);

    public function checkName($title);

    public function delete($slug,$resource_module_id);
}
