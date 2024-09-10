<?php

namespace App\Models;

use App\Notifications\MemberInvitationNotification;
use App\Notifications\NotificationTypes;
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
        'is_associated_member',
        'associated_component',
        'associated_component_id',
    ];

    public static function booted()
    {
        static::created(function (MemberManagement $management) {
            if (in_array($management->module_type, ['0', '1', '2'])) { // NOTIFY ONLY FOR CHALLENGE LAB AND ORGANIZATION
                /** HASH MAP FOR NOTIFICATION */
                $notificationTypeMapping = [
                    '0' => NotificationTypes::ORGANIZATION,
                    '1' => NotificationTypes::LAB,
                    '2' => NotificationTypes::CHALLENGE,
                ];
                /** @var User|null $user */
                $user = $management->user;
                $user?->notify(new MemberInvitationNotification( // NOTIFY ONLY IF THE USER EXISTS IN OUR SYSTEM
                    data_get($notificationTypeMapping, data_get($management, 'module_type')),
                    data_get($management, 'module_id'),
                    data_get($management, 'inviter_id'),
                    $management->module_type === '0' ? [
                        'role' => data_get($management, 'role'),
                    ] : null
                ));
            }
        });
    }

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
