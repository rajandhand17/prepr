<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengeTemplateTagsGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_template_tags_groups';

    protected $fillable = [
        'challenge_template_id',
        'foreign_id',
        'type',
    ];
}
