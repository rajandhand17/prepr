<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationUserPresenceStatus extends Model
{
    use HasFactory;

    protected $table = 'conversation_user_presence_status';

    protected $fillable = ['is_online', 'user_id'];
}
