<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'vendors';

    protected $fillable = [
        'id',
        'name',
        'email',
        'api_key',
        'secret_key',
        'is_active',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
