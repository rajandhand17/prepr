<?php

namespace App\Services\Manage;

use App\Models\ChallengeTagsGroups;
use Exception;

class ChallengeTagsGroupsService
{
    public function createChallengeTagsGroups($request, $challenge)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    foreach ($request->tags as $tag) {
                        $ChallengeTagsGroups = new ChallengeTagsGroups();
                        $ChallengeTagsGroups->challenge_id = $challenge;
                        $ChallengeTagsGroups->foreign_id = $tag;
                        $ChallengeTagsGroups->type = '0';
                        $ChallengeTagsGroups->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    foreach ($request->tag_groups as $tag_group) {
                        $ChallengeTagsGroups = new ChallengeTagsGroups();
                        $ChallengeTagsGroups->challenge_id = $challenge;
                        $ChallengeTagsGroups->foreign_id = $tag_group;
                        $ChallengeTagsGroups->type = '1';
                        $ChallengeTagsGroups->save();
                    }
                }
            }

            return true;
        } catch (Exception $th) {
            return false;
        }
    }
}
