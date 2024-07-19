<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\ResourceModule;

class SolrResourceModuleHelper extends SolrBaseHelper
{
    protected string $solrCollection = SolrCollection::RESOURCES;

    protected string $schemaName = 'resource';

    protected string $modelClass = ResourceModule::class;

    protected array $searchQueryFields = ['resource_module_title'];

    public function formatData($value): array
    {
        $skillsIds = $value->skills()->pluck('foreign_id');
        $tagsIds = $value->tags()->pluck('foreign_id');

        return [
            'id'                          => data_get($value, 'id'),
            'resource_module_title'       => data_get($value, 'title'),
            'resource_module_title_slug'  => data_get($value, 'slug'),
            'resource_module_language'    => data_get($value, 'language'),
            'resource_module_description' => data_get($value, 'description'),
            'resource_module_is_from_go1' => (bool) data_get($value, 'go1_course_id'),
            'resource_module_status'      => data_get($value, 'status'),
            'resource_module_user_id'     => data_get($value, 'user_id'),
            'resource_module_org_id'      => data_get($value, 'org_id'),
            'resource_module_is_global'   => (bool) data_get($value, 'is_global'),
            'resource_module_skills_id'   => $skillsIds,
            'resource_module_tags_id'     => $tagsIds,
            'resource_module_duration_id' => [data_get($value, 'duration_id')],
            'resource_module_level_id'    => [data_get($value, 'level_id')],
            'resource_module_privacy'     => data_get($value, 'privacy'),
        ];
    }
}
