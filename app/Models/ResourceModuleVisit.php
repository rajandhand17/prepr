<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModuleVisit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_module_visits';
    protected $fillable = [
        'user_id',
        'module_id',
        'module_asset_id',
        'asset_type',
    ];
}
