<?php

return [
    'chargebee_site' => env('CHARGEBEE_SITE'),
    'chargebee_key'  => env('CHARGEBEE_KEY'),
    'base_plan'      => env('BASE_PLAN'),

    //plans yearly keys
    'seed_plan_yearly'   => 'free-plan-CAD-Yearly',
    'sprout_plan_yearly' => 'Sprout-Plan-CAD-Yearly',
    'budd_plan_yearly'   => 'Budd-Plan-CAD-Yearly',
    'bloom_plan_yearly'  => 'Bloom-Plan-CAD-Yearly',
    'unlimited_plan'     => 'Unlimited-Plan-CAD-Yearly',

    //plans monthly keys
    'seed_plan'   => 'free-plan-CAD-Monthly',
    'sprout_plan' => 'Sprout-Plan-CAD-Monthly',
    'budd_plan'   => 'Budd-Plan-CAD-Monthly',
    'bloom_plan'  => 'Bloom-Plan-CAD-Monthly',
];
