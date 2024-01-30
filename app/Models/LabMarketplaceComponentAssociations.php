<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabMarketplaceComponentAssociations extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_marketplace_component_associations';

    protected $fillable = [
        'lab_marketplace_id',
        'lab_program_id',
        'challenge_template_id',
        'challenge_path_template_id',
        'resource_module_id',
        'resource_collection_id',
        'resource_group_id',
        'sequence',
    ];
}
