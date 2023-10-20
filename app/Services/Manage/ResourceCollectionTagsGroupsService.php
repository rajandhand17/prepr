<?php

namespace App\Services\Manage;

use App\Models\ResourceCollectionTagsGroups;

class ResourceCollectionTagsGroupsService
{
    public function createCollectionModuleTagsGroups($request, $resource_collection_id)
    {
        if ($request->has('tags')) {
            if (count($request->tags) > 0) {
                foreach ($request->tags as $tag) {
                    $resourceCollectionSkillGroupTag=new ResourceCollectionTagsGroups();
                    $resourceCollectionSkillGroupTag->resource_collection_id=$resource_collection_id;
                    $resourceCollectionSkillGroupTag->foreign_id = $tag;
                    $resourceCollectionSkillGroupTag->type = '0';
                    $resourceCollectionSkillGroupTag->save();
                }
            }
        }
        if ($request->has('tag_groups')) {
            if (count($request->tag_groups) > 0) {
                foreach ($request->tag_groups as $tag_group) {
                    $resourceCollectionSkillGroupTag=new ResourceCollectionTagsGroups();
                    $resourceCollectionSkillGroupTag->resource_collection_id = $resource_collection_id;
                    $resourceCollectionSkillGroupTag->foreign_id = $tag_group;
                    $resourceCollectionSkillGroupTag->type = '1';
                    $resourceCollectionSkillGroupTag->save();
                }
            }
        }
        return true;
    }



}
