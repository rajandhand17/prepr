<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'uuid',
        'name',
        'is_archived',
        'type',
        'group_photo',
        'is_private',
        'created_by'
    ];

    public function chats()
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'conversation_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user', 'conversation_id', 'user_id');
    }

    public function lastSeenMessage()
    {
        return $this->hasOne(UserMessageSeen::class, 'conversation_id', 'id')
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
                $participantName = "$user->first_name";
            }
        }

        return "Group($participantName)";
    }

    public function getDefaultConversationNameAttribute()
    {
        if ($this->type === 'groupMessage') {
            return $this->prepareGroupName($this->users()->get());
        }

        if ($this->type === 'directMessage') {
            $user = $this->users()->where('id', '!=', auth()->user()->id)->first();
            return $user->full_name;
        }

        return null;
    }

}
