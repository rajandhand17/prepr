<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'discussions';

    protected $fillable = [
        'id',
        'user_id',
        'module_id',
        'module_type',
        'comments',
        'attachment',
        'comment_id',
        'attachment_names',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function comments_reply()
    {
        return $this->hasMany(Discussion::class, 'comment_id', 'id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function liked_by()
    {
        return $this->hasMany(DiscussionSocialActivity::class, 'comment_id', 'id')->where('like_dislikes', '1');
    }

    public function disliked_by()
    {
        return $this->hasMany(DiscussionSocialActivity::class, 'comment_id', 'id')->where('like_dislikes', '2');
    }

    public function getAttachmentAttribute($attachments): array
    {
        if (empty($attachments)) {
            return [];
        }
        $aws_url = config('site-settings.aws_url');

        return array_map(function ($attachment) use ($aws_url) {
            return $aws_url.$attachment;
        }, json_decode($attachments));
    }

    public function getAttachmentNamesAttribute($attachment_names): array
    {
        if (empty($attachment_names)) {
            return [];
        }

        return json_decode($attachment_names);
    }
}
