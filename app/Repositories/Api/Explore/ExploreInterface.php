<?php

namespace App\Repositories\Api\Explore;

interface ExploreInterface
{

    public function recommended();

    public function getFeaturedLabs();

    public function recommendedSkills();
}

