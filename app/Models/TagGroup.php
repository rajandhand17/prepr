<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tag_groups';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'description',
        'fr_CA_description',
        'tags',
    ];

    protected $casts = [
        'tags' => 'json',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
