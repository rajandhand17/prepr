<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberManagement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'member_management';

    protected $fillable = [
        'uuid',
        'type',
        'invite_type',
        'module_id',
        'module_type',
        'inviter_id',
        'role',
        'invite_status',
        'email',
        'auto_invite',
        'invitee_name',
        'email_status',
        'email_response',
        'email_resend_status',
        'email_resend_count',
        'subject_line',
        'email_body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'id', 'module_id');
    }

    public function organizationAddress()
    {
        return $this->hasMany(OrganizationAddress::class, 'organization_id', 'module_id');
    }

    public function organizationUser()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
