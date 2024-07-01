<?php

namespace App\Models;

use App\Helpers\UtilityHelper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_settings';

    protected $fillable = [
        'user_id', 'profile_privacy', 'friend_request_privacy', 'project_privacy', 'manage_alerts', 'is_subscribe', 'newsfeeds', 'email_subscription_notification', 'email_subscription_network_summary', 'email_subscription_challenge_summary', 'email_subscription_lab_summary', 'display_lab_minionboarding', 'display_challenge_minionboarding', 'display_challenge_minionboarding', 'display_org_minionboarding', '	fcm_notification_permission', 'fcm_device_token', 'challenge_recommends',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public static function create(User $user, $request)
    {
        try {
            DB::beginTransaction();
            $usersetting = new UserSetting();
            $usersetting->user_id = $user->id;
            $usersetting->save();
            if ($usersetting) {
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();

            return false;
        }
    }
}
