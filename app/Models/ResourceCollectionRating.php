<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollectionRating extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'resource_collection_ratings';

    protected $fillable = [
        'resource_collection_id',
        'user_id',
        'rating',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function resource_rating()
    {
        if (auth('api')->check()) {
            return $this->hasOne(ResourceCollectionRating::class, 'resource_collection_id', 'id')->where('user_id', auth('api')->user()->id);
        }

        return 'N/A';
    }
}
