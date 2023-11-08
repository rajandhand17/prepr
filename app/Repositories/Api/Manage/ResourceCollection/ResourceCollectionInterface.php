<?php

namespace App\Repositories\Api\Manage\ResourceCollection;

interface ResourceCollectionInterface
{
    public function createResourceCollection($request, $upload_media);

    public function uploadResourceCollectionCoverImage($cover_image);

    public function getResourceCollectionBasedOnSlug($slug);

    public function checkName($title);

    public function getResourceCollectionList($request, $organization);

    public function getLabListName($request, $organization);
}
