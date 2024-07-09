<?php

use App\Helpers\Solr\Constants\SolrCollection;

return [
    'collection' => SolrCollection::CHALLENGE_PATHS,
    'schema'     => [
        [
            'name'        => 'challenge_path_title',
            'type'        => 'text_edge_ngram',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_path_language',
            'type'        => 'text_general',
            'indexed'     => true,
            'multiValued' => false,
            'stored'      => true,
        ],
        [
            'name'        => 'challenge_path_organisation_id',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_path_user_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_path_status',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_path_privacy',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_path_description',
            'type'        => 'text_general',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => false,
        ],
        [
            'name'        => 'challenge_path_skills_id',
            'type'        => 'string',
            'indexed'     => true,
            'stored'      => true,
            'multiValued' => true,
        ],
        [
            'name'        => 'challenge_path_duration_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'        => 'challenge_path_level_id',
            'type'        => 'string',
            'indexed'     => true,
            'multiValued' => true,
            'stored'      => true,
        ],
        [
            'name'    => 'challenge_path_is_accessible',
            'type'    => 'boolean',
            'indexed' => true,
            'stored'  => true,
        ],
        [
            'name'    => 'challenge_path_category_id',
            'type'    => 'string',
            'indexed' => true,
            'stored'  => true,
        ],
    ],
];
