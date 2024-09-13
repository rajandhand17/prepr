<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceGroupRating extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_group_ratings';

    protected $fillable = [
        'resource_collection_id',
        'user_id',
        'rating',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function resource_rating()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ResourceGroupRating::class, 'resource_collection_id', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'N/A';
    }
}
