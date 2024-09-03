<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rank extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'ranks';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'description',
        'fr_CA_description',
        'image',
        'category',
        'point',
        'no_of_use',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
