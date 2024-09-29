<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectMemberManagement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_member_management';
    protected $fillable = [
        'uuid',
        'project_id',
        'inviter_id',
        'email',
        'auto_invite',
        'invitee_name',
        'invite_type',
        'invite_status',
        'email_status',
        'email_response',
        'email_resend_status',
        'inviter_access_level',
        'subject_line',
        'email_body',
    ];
}
