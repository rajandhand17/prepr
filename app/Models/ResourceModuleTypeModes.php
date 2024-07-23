<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModuleTypeModes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_module_type_modes';
    protected $fillable = [
        'resource_module_id',
        'type_mode',
        'value',
    ];

}
