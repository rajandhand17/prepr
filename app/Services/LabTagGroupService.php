<?php

namespace App\Services;

use App\Models\Friend;
use App\Models\LabTagsGroups;

class LabTagGroupService
{

    public function getTrendingTopics(){
        try {
            $getTopTags=LabTagsGroups::where('type','0')->limit(10);
            return $getTopTags->paginate(config('site-settings.pagination_per_page'));
        }catch (\Exception $e){
            return false;
        }
    }
}
