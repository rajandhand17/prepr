<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTemplateTagsGroups extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'template_lab_tags_groups';

    protected $fillable = [
        'template_lab_id',
        'foreign_id',
        'type',
    ];
}
