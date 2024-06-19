<?php

namespace App\Repositories\Api\Public\Skill;

interface SkillInterface
{
    public function index($language, $search, $sortBy, $skillId);

    public function getMySkills($language, $search, $pinned, $sortBy);

    public function getSkillBasedOnId($skillId);
}
