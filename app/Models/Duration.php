<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Duration extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'durations';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'min_minutes',
        'max_minutes',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
