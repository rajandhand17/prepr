<?php

return [
    'openai_api_key'                    => env('OPENAI_API_KEY'),
    'bing_api_key'                      => env('BING_API_KEY'),
    'skills_recommendation_engine_url'  => env('SKILLS_RECOMMENDATION_ENGINE_URL'),
    'skills_recommendation_engine_key'  => env('RELATED_SKILLS_AUTH_TOKEN'),
    'resource_summarizer_api_key'       => env('RESOURCE_SUMMARIZER_API_KEY'),

    'openai_endpoint'                   => env('OPENAI_ENDPOINT'),
    'bing_default_endpoint'             => env('BING_DEFAULT_ENDPOINT'),
    'bing_video_endpoint'               => env('BING_VIDEO_ENDPOINT'),
    'resource_summarizer_endpoint'      => env('RESOURCE_SUMMARIZER_ENDPOINT'),
    'project_assessor_endpoint'         => env('PROJECT_ASSESSOR_ENDPOINT'),
    'project_assessor_api_key'          => env('PROJECT_ASSESSOR_API_KEY'),
];
