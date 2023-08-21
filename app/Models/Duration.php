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
    ];

}
