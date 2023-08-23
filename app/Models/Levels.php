<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Levels extends Model
{
    use HasFactory;
    use softDeletes;

    protected $table = 'levels';
    protected $fillable = [
        'title',
        'fr_CA_title',
    ];
}
