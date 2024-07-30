<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollectionTypeModes extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_collection_type_modes';
    protected $fillable = [
        'resource_collection_id',
        'type_mode',
        'value',
    ];
}
