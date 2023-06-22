<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberManagement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'member_management';

    protected $fillable = [
        'type',
        'invite_type',
        'module_id',
        'module_type',
        'inviter_id',
        'invitee_id',
        'role',
        'invite_status',
        'email',
        'email_status',
        'email_response',
        'email_resend_status',
        'email_resend_count',
        'subject_line',
        'email_body',
    ];

    /***
       * @return HasOne
       */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'inviter_id');
    }

    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'module_id');
    }

}
