<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallengePathTemplateTagsGroups extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'challenge_path_template_tags_groups';
    protected $fillable = [
        'challenge_path_template_id',
        'foreign_id',
        'type',
    ];
}
