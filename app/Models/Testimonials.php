<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonials extends Model
{
    use HasFactory;

    protected $table = 'testimonials';

    protected $fillable = [
        'user_id',
        'description',
        'status'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
