<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\ResourceGroup;

class SolrResourceGroupHelper extends SolrBaseHelper
{
    protected array $searchQueryFields = ['resource_group_title'];

    protected string $solrCollection = SolrCollection::RESOURCE_GROUPS;

    protected string $modelClass = ResourceGroup::class;

    protected string $schemaName = 'resource_group';

    public function formatData($value): array
    {
        $skillsIds = $value->skills()->pluck('foreign_id');

        return [
            'id'                             => data_get($value, 'id'),
            'resource_group_title'           => data_get($value, 'title'),
            'resource_group_language'        => data_get($value, 'language'),
            'resource_group_organisation_id' => data_get($value, 'organisation'),
            'resource_group_user_id'         => data_get($value, 'user_id'),
            'resource_group_status'          => data_get($value, 'status'),
            'resource_group_privacy'         => data_get($value, 'privacy'),
            'resource_group_description'     => data_get($value, 'description'),
            'resource_group_skills_id'       => $skillsIds,
            'resource_group_duration_id'     => [data_get($value, 'duration')],
            'resource_group_level_id'        => [data_get($value, 'level')],
            'resource_group_is_accessible'   => data_get($value, 'is_accessible'),
        ];
    }
}
