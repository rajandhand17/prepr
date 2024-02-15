<?php

namespace App\Repositories\Api\Public\Skill;

interface SkillInterface
{
    public function index($language, $search, $skillId);

    public function getMySkills($language, $search);

    public function getSkillBasedOnId($skillId);
}
