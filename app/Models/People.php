<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class People extends Model
{
    use HasFactory; 
    use SoftDeletes;

    protected $table = 'people';
    
    protected $fillable = [
        'org_id',
        'name',
        'description',
        'image',
    ];

}
