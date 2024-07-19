<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedLab extends Model
{
    use HasFactory;

    protected $table = 'featured_labs';

    protected $fillable = [
        'lab_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
