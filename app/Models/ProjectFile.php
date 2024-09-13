<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectFile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_files';
    protected $fillable = [
        'project_id',
        'title',
        'path',
        'type',
    ];

    public function getPathAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
