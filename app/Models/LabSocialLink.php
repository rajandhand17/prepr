<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabSocialLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'lab_social_link';

    protected $fillable = [
        'user_id',
        'lab_id',
        'social_link_id',
        'link_url',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
