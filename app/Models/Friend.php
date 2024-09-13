<?php

namespace App\Models;

use App\Notifications\FriendRequestNotification;
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

    /**
     * @return void
     */
    public static function booted(): void
    {
        static::created(function (Friend $friendRequest) {
            /** @var User|null $user */
            $user = $friendRequest->getFriendsProfileBasedOnUserId()->first();
            $user?->notify(new FriendRequestNotification( // NOTIFY ONLY IF THE USER EXISTS IN OUR SYSTEM
                data_get($friendRequest, 'reference_id'),
                data_get($friendRequest, 'id'),
            ));
        });
    }

    public function getFriendsProfilebasedOnReference()
    {
        return $this->belongsTo(User::class, 'reference_id', 'id');
    }

    public function getFriendsProfileBasedOnUserId()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
