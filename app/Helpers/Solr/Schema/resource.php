<?php

use App\Helpers\Solr\Constants\SolrCollection;

return [
    'collection' => SolrCollection::RESOURCES,
    'schema'     => [
        [
            'name'        => 'resource_module_title',
            'type'        => 'text_edge_ngram',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_module_title_slug',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_module_language',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_module_description',
            'type'        => 'text_general',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
        [
            'name'    => 'resource_module_is_from_go1',
            'type'    => 'boolean',
            'indexed' => true,
            'stored'  => true,
        ],
        [
            'name'        => 'resource_module_status',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_module_user_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'resource_module_org_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'    => 'resource_module_is_global',
            'type'    => 'boolean',
            'indexed' => true,
            'stored'  => true,
        ],
        [
            'name'        => 'resource_module_skills_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
        [
            'name'        => 'resource_module_tags_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
        [
            'name'        => 'resource_module_duration_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_module_level_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'        => 'resource_module_privacy',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
    ],
];
