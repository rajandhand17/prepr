<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationSeenMessage extends Model
{
    use HasFactory;

    protected $table = 'conversation_seen_messages';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'message_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(ConversationMessage::class, 'message_id', 'id');
    }
}
