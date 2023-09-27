<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModuleRating extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_module_ratings';
    protected $fillable = [
        'resource_module_id',
        'user_id',
        'rating',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
