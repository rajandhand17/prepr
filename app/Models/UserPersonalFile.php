<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPersonalFile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_personal_files';

    protected $fillable = [
        'user_id',
        'original',
        'name',
        'path',
        'public',
    ];

    public function getNameAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
