<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChargebeeSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'chargebee_subscriptions';
    protected $fillable = [
        'organization_id',
        'plan',
        'plan_validity',
        'plan_limitations',
        'trial_end_date',
        'challenge_limits',
        'challenge_path_limits',
        'lab_limits',
        'lab_program_limits',
        'pre_build_lab_limits',
        'resource_module_limits',
        'resource_collection_limits',
        'resource_group_limits',
        'user_invite_limits',
        'organization_invite_limits',
    ];
}
