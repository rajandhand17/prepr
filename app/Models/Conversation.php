<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

class Conversation extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'conversations';

    protected $fillable = [
        'uuid',
        'is_archived',
        'type',
        'group_photo',
        'is_private',
        'created_by',
    ];

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'message.conversation.'.$this->id;
    }

    public function chats()
    {
        return $this->hasMany(ConversationMessage::class, 'conversation_id', 'id');
    }

    public function lastMessage()
    {
        return $this->hasOne(ConversationMessage::class, 'conversation_id', 'id')->orderBy('updated_at', 'DESC');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_users', 'conversation_id', 'user_id');
    }

    public function lastSeenMessage()
    {
        return $this->hasOne(ConversationSeenMessage::class, 'conversation_id', 'id')
            ->with('chat')
            ->where('user_id', auth()->user()->id);
    }

    public function getIsConversationSeenAttribute()
    {
        $lastMessageInConversation = $this->lastMessage()->first();
        $lastSeenMessage = $this->lastSeenMessage()->first();

        if (!$lastSeenMessage || !$lastMessageInConversation) {
            return false;
        }

        return $lastMessageInConversation->id === $lastSeenMessage->message_id;
    }

    private function prepareGroupName(Collection $users): string
    {
        $participantName = '';

        foreach ($users as $user) {
            if ($participantName) {
                $participantName = "$participantName,$user->first_name";
            } else {
                $participantName = $user->first_name;
            }
        }

        return "Group($participantName)";
    }

    public function getDefaultConversationNameAttribute()
    {
        if ($this->type === 'group') {
            return $this->prepareGroupName($this->users()->get());
        }

        if ($this->type === 'direct_message') {
            $user = $this->users()->where('id', '!=', auth()->user()->id)->first();

            return $user->full_name ?? sprintf('%s %s', $user->first_name, $user->last_name);
        }

        return null;
    }

    public function getTypeAttribute($value)
    {
        return config('constants.conversation_type_id.'.$value);
    }

    public function getIsOnlineAttribute()
    {
        $users = $this->users()->whereHas('presence', function ($query) {
            $query->where('is_online', true);
        })->where('id', '!=', auth()->user()->id)->get();

        if (count($users)) {
            return true;
        }

        return false;
    }
}
