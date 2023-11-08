<?php

namespace App\Repositories\Api\Public\Achievement;

interface AchievementInterface
{
    public function getList($request);
    public function getAchievementBasedOnSlug($id);

}
