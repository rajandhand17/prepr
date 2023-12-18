<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateComponentAssociation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'template_component_associations';

    protected $fillable = [
        'template_lab_id',
        'template_lab_program_id',
        'template_challenge_id',
        'template_challenge_path_id',
        'template_resource_module_id',
        'template_resource_collection_id',
        'template_resource_group_id',
        'sequence',
    ];
}
