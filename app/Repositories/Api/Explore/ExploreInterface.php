<?php

namespace App\Repositories\Api\Explore;

interface ExploreInterface
{
    public function recommendedLabsAndChallenges();

    public function getFeaturedModule();

    public function recommendedSkills($getUserSkills);

    public function trendingJobs();
}
