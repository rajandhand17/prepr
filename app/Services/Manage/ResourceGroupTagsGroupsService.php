<?php

namespace App\Services\Manage;

use App\Helpers\UtilityHelper;
use App\Models\ResourceGroupTagGroups;

class ResourceGroupTagsGroupsService
{
    public static function createResourceGroupTagsGroups($request, $resource_group_id)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    foreach ($request->tags as $tag) {
                        $resourceGroupTag = new ResourceGroupTagGroups();
                        $resourceGroupTag->resource_group_id = $resource_group_id;
                        $resourceGroupTag->foreign_id = $tag;
                        $resourceGroupTag->type = '0';
                        $resourceGroupTag->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    foreach ($request->tag_groups as $tag_group) {
                        $resourceGroupTagGroups = new ResourceGroupTagGroups();
                        $resourceGroupTagGroups->resource_group_id = $resource_group_id;
                        $resourceGroupTagGroups->foreign_id = $tag_group;
                        $resourceGroupTagGroups->type = '1';
                        $resourceGroupTagGroups->save();
                    }
                }
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public static function deleteResourceGroupTagsGroups($resource_group_id)
    {
        try {
            $checkExistsResourceGroupTagsGroups = ResourceGroupTagGroups::select('id')->where('resource_group_id', $resource_group_id)->pluck('id');
            if ($checkExistsResourceGroupTagsGroups) {
                $deleteResourceGroupTagsGroups = ResourceGroupTagGroups::whereIn('id', $checkExistsResourceGroupTagsGroups)->delete();
                if (!$deleteResourceGroupTagsGroups) {
                    return false;
                }
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }

    public function updateResourceGroupTagsGroups($request, $updateResourceGroupId)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    $getExistsGroupCollectionTags = ResourceGroupTagGroups::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '0'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsGroupCollectionTags, $request->tags);
                    $deleteNonExisting = ResourceGroupTagGroups::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '0'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTagsGroups = array_diff($request->tags, $getExistsGroupCollectionTags);
                    foreach ($newTagsGroups as $tag) {
                        $resourceGroupTag = new ResourceGroupTagGroups();
                        $resourceGroupTag->resource_group_id = $updateResourceGroupId;
                        $resourceGroupTag->foreign_id = $tag;
                        $resourceGroupTag->type = '0';
                        $resourceGroupTag->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    $getExistsResourceCollectionTagsGroups = ResourceGroupTagGroups::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '1'],
                    ])->pluck('foreign_id')->toArray();
                    $nonExistingIds = array_diff($getExistsResourceCollectionTagsGroups, $request->tag_groups);
                    $deleteNonExisting = ResourceGroupTagGroups::where([
                        ['resource_group_id', '=', $updateResourceGroupId],
                        ['type', '=', '1'],
                    ])->whereIn('foreign_id', $nonExistingIds)->delete();
                    $newTagsGroups = array_diff($request->tag_groups, $getExistsResourceCollectionTagsGroups);
                    foreach ($newTagsGroups as $tag_group) {
                        $resourceGroupTagGroups = new ResourceGroupTagGroups();
                        $resourceGroupTagGroups->resource_group_id = $updateResourceGroupId;
                        $resourceGroupTagGroups->foreign_id = $tag_group;
                        $resourceGroupTagGroups->type = '1';
                        $resourceGroupTagGroups->save();
                    }
                }
            }

            return true;
        } catch(\Exception $e) {
            UtilityHelper::logError($e);

            return false;
        }
    }
}
