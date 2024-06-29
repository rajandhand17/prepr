<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
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
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function updateChallengePathTagsGroupsService($request, $challengePathId)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    $getExistsChallengePathTags = ChallengePathTagGroup::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsChallengePathTags, $request->tags);
                    $deleteNonExisting = ChallengePathTagGroup::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTags = array_diff($request->tags, $getExistsChallengePathTags);
                    foreach ($newTags as $tag) {
                        $challengePathTags = new ChallengePathTagGroup();
                        $challengePathTags->challenge_path_id = $challengePathId;
                        $challengePathTags->foreign_id = $tag;
                        $challengePathTags->type = '0';
                        $challengePathTags->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    $getExistsChallengePathTagsGroups = ChallengePathTagGroup::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->all();
                    $nonExistingIds = array_diff($getExistsChallengePathTagsGroups, $request->tag_groups);
                    $deleteNonExisting = ChallengePathTagGroup::where([
                        ['challenge_path_id', '=', $challengePathId],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTagsGroups = array_diff($request->tag_groups, $getExistsChallengePathTagsGroups);
                    foreach ($newTagsGroups as $tag_group) {
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
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function deleteChallengePathTagGroup($challengePathId)
    {
        try {
            $challengePathIds = ChallengePathTagGroup::where('challenge_path_id', $challengePathId)->pluck('id');
            if ($challengePathIds->isNotEmpty()) {
                $challengePathTagGroup = ChallengePathTagGroup::whereIn('id', $challengePathIds)->delete();
                if (!$challengePathTagGroup) {
                    return false;
                }

                return true;
            }

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
