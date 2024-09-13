<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabChallengeRedeem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_challenge_redeems';
    protected $fillable = [
        'user_id',
        'organization_id',
        'lab_id',
        'lab_marketplace_id',
        'challenge_id',
        'challenge_template_id',
        'is_redeemed',
    ];
}
