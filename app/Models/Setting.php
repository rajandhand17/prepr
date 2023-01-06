<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'label',
        'value',
        'hidden',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

}
