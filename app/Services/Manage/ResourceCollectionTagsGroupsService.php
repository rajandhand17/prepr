<?php

namespace App\Services\Manage;

use App\Models\ResourceCollectionTagsGroups;

class ResourceCollectionTagsGroupsService
{
    public function createCollectionModuleTagsGroups($request, $resource_collection_id)
    {
        try {
            if ($request->has('tags')) {
                if (count($request->tags) > 0) {
                    foreach ($request->tags as $tag) {
                        $resourceCollectionTag = new ResourceCollectionTagsGroups();
                        $resourceCollectionTag->resource_collection_id = $resource_collection_id;
                        $resourceCollectionTag->foreign_id = $tag;
                        $resourceCollectionTag->type = '0';
                        $resourceCollectionTag->save();
                    }
                }
            }
            if ($request->has('tag_groups')) {
                if (count($request->tag_groups) > 0) {
                    foreach ($request->tag_groups as $tag_group) {
                        $resourceCollectionTagGroups = new ResourceCollectionTagsGroups();
                        $resourceCollectionTagGroups->resource_collection_id = $resource_collection_id;
                        $resourceCollectionTagGroups->foreign_id = $tag_group;
                        $resourceCollectionTagGroups->type = '1';
                        $resourceCollectionTagGroups->save();
                    }
                }
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public static function deleteResourceCollectionTagsGroups($resource_collection_id)
    {
        try {
            $checkExistsResourceCollectionTagsGroups = ResourceCollectionTagsGroups::select('id')->where('resource_collection_id', $resource_collection_id)->pluck('id');
            if ($checkExistsResourceCollectionTagsGroups) {
                $deleteResourceCollectionTagsGroups = ResourceCollectionTagsGroups::whereIn('id', $checkExistsResourceCollectionTagsGroups)->delete();
                if (!$deleteResourceCollectionTagsGroups) {
                    return false;
                }
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}
