<?php

namespace App\Repositories\Api\Manage\ResourceCollection;

interface ResourceCollectionInterface
{
    public function getResourceCollectionCountBasedOnOrganization($organizationId);

    public function createResourceCollection($request, $upload_cover_image, $organizationId);

    public function uploadResourceCollectionCoverImage($cover_image);

    public function getResourceCollectionBasedOnSlug($slug);

    public function checkName($title);

    public function updateResourceCollection($slug, $request, $upload_cover_image, $organizationId);

    public function getResourceCollectionList($request, $organization);

    public function deleteResourceCollection($resource_collection_id);

    public function getListName($request, $organization);
}
