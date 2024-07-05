<?php

namespace App\Helpers\Solr;

use App\Helpers\Solr\Constants\SolrCollection;
use App\Models\ResourceCollection;

class SolrResourceCollectionHelper extends SolrBaseHelper
{
    protected array $searchQueryFields = ['resource_collection_title'];

    protected string $solrCollection = SolrCollection::RESOURCE_COLLECTIONS;

    protected string $modelClass = ResourceCollection::class;

    protected string $schemaName = 'resource_collection';

    public function formatData($value): array
    {
        $skillsIds = $value->skills()->pluck('foreign_id');

        return [
            'id'                                  => data_get($value, 'id'),
            'resource_collection_title'           => data_get($value, 'title'),
            'resource_collection_slug'            => data_get($value, 'slug'),
            'resource_collection_language'        => data_get($value, 'language'),
            'resource_collection_organisation_id' => data_get($value, 'org_id'),
            'resource_collection_user_id'         => data_get($value, 'user_id'),
            'resource_collection_status'          => data_get($value, 'status'),
            'resource_collection_is_accessible'   => data_get($value, 'is_accessible'),
            'resource_collection_description'     => data_get($value, 'description'),
            'resource_collection_skills_id'       => $skillsIds,
            'resource_collection_duration_id'     => [data_get($value, 'duration')],
            'resource_collection_level_id'        => [data_get($value, 'level')],
            'resource_collection_privacy'         => data_get($value, 'privacy'),
        ];
    }
}
