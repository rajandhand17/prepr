<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceGroupTagGroups extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'resource_group_tags_groups';

    protected $fillable = [
        'resource_group_id',
        'foreign_id',
        'type',
    ];
}
