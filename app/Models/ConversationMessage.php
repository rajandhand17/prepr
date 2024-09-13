<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConversationMessage extends Model
{
    use HasFactory;

    protected $table = 'conversation_messages';

    protected $casts = ['attachments' => 'object'];

    protected $fillable = [
        'uuid',
        'conversation_id',
        'message',
        'attachments',
        'status',
        'sender_id',
        'deleted_at',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function seenUsers(): HasMany
    {
        return $this->hasMany(ConversationSeenMessage::class, 'message_id', 'id')->with('user');
    }

    public function getChatSeenUserAttribute()
    {
        return $this->seenUsers->pluck('user');
    }

    public function getAttachmentsAttribute($value)
    {
        $attachments = $this->castAttribute('attachments', $value);

        return collect($attachments)->map(function ($item) {
            return config('site-settings.aws_url').$item;
        });
    }

    public function getIsSenderAttribute()
    {
        if ($this->sender_id === auth()->user()->id) {
            return true;
        }

        return false;
    }
}
