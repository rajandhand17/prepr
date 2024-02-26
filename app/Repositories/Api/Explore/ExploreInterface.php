<?php

namespace App\Repositories\Api\Explore;

interface ExploreInterface
{
    public function index($request);
    public function recommended($request);
}

