<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
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
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function updateChallengeTagsGroups($request, $challenge_id)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    $getExistsChallengeTags = ChallengeTagsGroups::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsChallengeTags, $request->tags);
                    $deleteNonExisting = ChallengeTagsGroups::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTags = array_diff($request->tags, $getExistsChallengeTags);
                    foreach ($newTags as $tag) {
                        $challengeSkillsGroupsStack = new ChallengeTagsGroups();
                        $challengeSkillsGroupsStack->challenge_id = $challenge_id;
                        $challengeSkillsGroupsStack->foreign_id = $tag;
                        $challengeSkillsGroupsStack->type = '0';
                        $challengeSkillsGroupsStack->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    $getExistsChallengeTagsGroups = ChallengeTagsGroups::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsChallengeTagsGroups, $request->tag_groups);
                    $deleteNonExisting = ChallengeTagsGroups::where([
                        ['challenge_id', '=', $challenge_id],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTagsGroups = array_diff($request->tag_groups, $getExistsChallengeTagsGroups);
                    foreach ($newTagsGroups as $tag_group) {
                        $challengeSkillsGroupsStack = new ChallengeTagsGroups();
                        $challengeSkillsGroupsStack->challenge_id = $challenge_id;
                        $challengeSkillsGroupsStack->foreign_id = $tag_group;
                        $challengeSkillsGroupsStack->type = '1';
                        $challengeSkillsGroupsStack->save();
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function cloneChallengeTags($originalChallengeTags, $clonedChallengeId)
    {
        try {
            $originalChallengeTags->each(function ($tags) use ($clonedChallengeId) {
                if ($tags) {
                    $cloneTag = $tags->replicate();
                    $cloneTag->challenge_id = $clonedChallengeId;
                    $cloneTag->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public function cloneChallengeTagsGroups($originalChallengeTagGroups, $clonedChallengeId)
    {
        try {
            $originalChallengeTagGroups->each(function ($tag_groups) use ($clonedChallengeId) {
                if ($tag_groups) {
                    $cloneTagGroups = $tag_groups->replicate();
                    $cloneTagGroups->challenge_id = $clonedChallengeId;
                    $cloneTagGroups->save();
                }
            });

            return true;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }

    public static function getChallengeIdBasedOnSkills($tags)
    {
        try {
            $getChallengeIds = ChallengeTagsGroups::where('type', '0')
                ->whereIn('foreign_id', $tags)
                ->pluck('challenge_id');

            return $getChallengeIds;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
