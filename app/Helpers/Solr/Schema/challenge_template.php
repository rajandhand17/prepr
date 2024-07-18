<?php

use App\Helpers\Solr\Constants\SolrCollection;

return [
    'collection' => SolrCollection::CHALLENGE_TEMPLATES,
    'schema'     => [
        [
            'name'        => 'challenge_template_title',
            'type'        => 'text_edge_ngram',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_language',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_slug',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_user_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_organization_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_description',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_category_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_skills_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
        [
            'name'        => 'challenge_template_status',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_open_status',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_tags_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
        [
            'name'        => 'challenge_template_privacy',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_level_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_template_duration_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
    ],
];
