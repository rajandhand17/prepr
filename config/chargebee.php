<?php

return [
    'chargebee_site' => env('CHARGEBEE_SITE'),
    'chargebee_key'  => env('CHARGEBEE_KEY'),
    'base_plan'      => env('BASE_PLAN'),

    //plans yearly keys
    'chargebee_plan' => [
        'seed_plan_yearly'   => 'free-plan-CAD-Yearly',
        'sprout_plan_yearly' => 'Sprout-Plan-CAD-Yearly',
        'budd_plan_yearly'   => 'Budd-Plan-CAD-Yearly',
        'bloom_plan_yearly'  => 'Bloom-Plan-CAD-Yearly',
        'unlimited_plan'     => 'Unlimited-Plan-CAD-Yearly',
    ],

    //plans yearly keys
    'chargebee_plan_monthly' => [
        'seed_plan_monthly'   => 'free-plan-CAD-Monthly',
        'sprout_plan_monthly' => 'Sprout-Plan-CAD-Monthly',
        'budd_plan_monthly'   => 'Budd-Plan-CAD-Monthly',
        'bloom_plan_monthly'  => 'Bloom-Plan-CAD-Monthly',
        'unlimited_plan'      => 'Unlimited-Plan-CAD-Monthly',
    ],

    // Addon Yearly Keys
    'chargebee_addon' => [
        'challenge_addon_yearly'           => 'challenge-creation-CAD-Yearly',
        'challenge_path_addon_yearly'      => 'Challenge-Path-Creation-CAD-Yearly',
        'lab_addon_yearly'                 => 'Lab-Creation-CAD-Yearly',
        'lab_program_addon_yearly'         => 'Lab-Program-Creation-CAD-Yearly',
        'resource_module_addon_yearly'     => 'Resource-Creation-CAD-Yearly',
        'resource_collection_addon_yearly' => 'Resource-Collection-Creation-CAD-Yearly',
        'resource_group_addon_yearly'      => 'Resource-Group-Creation-CAD-Yearly',
        'paid_lab_addon_yearly'            => 'Paid-Lab-CAD-Yearly',
        'manager_addon_yearly'             => 'Manager-Invite-CAD-Yearly',
        'user_addon_yearly'                => 'User-Invite-CAD-Yearly',
    ],
];
