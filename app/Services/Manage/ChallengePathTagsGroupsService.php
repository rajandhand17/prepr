<?php

namespace App\Services\Manage;

use App\Models\ChallengePathTagGroup;
use Exception;

class ChallengePathTagsGroupsService
{
    public function createChallengePathTagsGroupsService($request, $challengePathId)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    foreach ($request->tags as $tag) {
                        $challengePathTagGroups = new ChallengePathTagGroup();
                        $challengePathTagGroups->challenge_path_id = $challengePathId;
                        $challengePathTagGroups->foreign_id = $tag;
                        $challengePathTagGroups->type = '0';
                        $challengePathTagGroups->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    foreach ($request->tag_groups as $tag_group) {
                        $challengePathTagGroups = new ChallengePathTagGroup();
                        $challengePathTagGroups->challenge_path_id = $challengePathId;
                        $challengePathTagGroups->foreign_id = $tag_group;
                        $challengePathTagGroups->type = '1';
                        $challengePathTagGroups->save();
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
