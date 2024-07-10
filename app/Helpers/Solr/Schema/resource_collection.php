<?php

use App\Helpers\Solr\Constants\SolrCollection;

return [
    'collection' => SolrCollection::RESOURCE_COLLECTIONS,
    'schema'     => [
        [
            'name'        => 'resource_collection_title',
            'type'        => 'text_edge_ngram',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_collection_slug',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_collection_language',
            'type'        => 'text_general',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_collection_organisation_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_collection_user_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_collection_status',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_collection_is_accessible',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_collection_description',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_collection_skills_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
        [
            'name'        => 'resource_collection_duration_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_collection_level_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_collection_privacy',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
    ],
];
