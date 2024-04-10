<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationMember extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'organization_members';

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'position',
        'image',
    ];

    public function getImageAttribute($value)
    {
        return config('site-settings.aws_url').$value;
    }
}
