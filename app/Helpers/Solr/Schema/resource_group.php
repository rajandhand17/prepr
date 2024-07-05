<?php

use App\Helpers\Solr\Constants\SolrCollection;

return [
    'collection' => SolrCollection::RESOURCE_GROUPS,
    'schema'     => [
        [
            'name'        => 'resource_group_title',
            'type'        => 'text_edge_ngram',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_group_language',
            'type'        => 'text_general',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_group_organisation_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_group_user_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_group_status',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_group_privacy',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_group_description',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_group_skills_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],

        [
            'name'        => 'resource_group_duration_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_group_level_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_group_is_accessible',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
    ],
];
