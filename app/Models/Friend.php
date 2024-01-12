<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'friends';

    protected $fillable = [
        'user_id',
        'reference_id',
        'status',
        'user_follow',
        'reference_follow',
        'newsfeed',
    ];

    public function getFriendsProfilebasedOnReference()
    {
        return $this->belongsTo(User::class, 'reference_id', 'id');
    }

    public function getFriendsProfileBasedOnUserId()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
