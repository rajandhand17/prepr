<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Host extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'hosts';

    protected $fillable = [
        'title',
        'link',
        'image',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getImageAttribute($value)
    {
        if ($value) {
            return config('site-settings.aws_url').$value;
        }

        return null;
    }
}
