<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationInviteUser extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table="organization_invite_users";

    protected $fillable=[
        "organisation_id",
        "user_id",
        "inviter_id",
        "challenge_ids",
        "lab_ids",
        "resource_ids",
        "role",
        "email",
        "status",
        "invite_type",
        "invitation_status",
        "invite_status",
        "email_status",
        "email_responce",
        "email_resend_status",
        "subject_line",
        "email_message",
        "fail_schedule",
    ];

    protected $hidden=[
        "created_at","updated_at","deleted_at"
    ];
}
