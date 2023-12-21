<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateTagsGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_challenge_tags_groups';

    protected $fillable = [
        'template_challenge_id',
        'foreign_id',
        'type',
    ];
}
