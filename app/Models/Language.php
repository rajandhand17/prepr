<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'languages';

    protected $fillable = [
        'name',
        'iso',
        'status',
        'is_imported',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
