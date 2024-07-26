<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceGroupTypeModes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_group_type_modes';
    protected $fillable = [
        'resource_group_id',
        'type_mode',
        'value',
    ];
}
