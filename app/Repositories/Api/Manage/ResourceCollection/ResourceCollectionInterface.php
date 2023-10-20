<?php

namespace App\Repositories\Api\Manage\ResourceCollection;

interface ResourceCollectionInterface
{
    public function createResourceCollection($request, $upload_media);

    public function uploadResourceCollectionCoverImage($cover_image);

}
