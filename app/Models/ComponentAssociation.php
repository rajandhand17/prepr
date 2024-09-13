<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentAssociation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'component_associations';

    protected $fillable = [
        'lab_id',
        'lab_program_id',
        'challenge_id',
        'challenge_path_id',
        'resource_module_id',
        'resource_collection_id',
        'resource_group_id',
        'sequence',
    ];
}
