<?php

use App\Helpers\Solr\Constants\SolrCollection;

return [
    'collection' => SolrCollection::LAB_MARKETPLACE,
    'schema'     => [
        [
            'name'        => 'lab_marketplace_title',
            'type'        => 'text_edge_ngram',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_slug',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_language',
            'type'        => 'text_general',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
        [
            'name'        => 'lab_marketplace_organization_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_user_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_status',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_is_verified',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_privacy',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_description',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_skills_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
        [
            'name'        => 'lab_marketplace_category_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'lab_marketplace_duration_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ], [
            'name'        => 'lab_marketplace_level_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
    ],
];
