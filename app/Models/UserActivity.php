<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_activity';

    // Define which attributes are mass assignable
    protected $fillable = [
        'user_id',
        'activity_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a user activity if it does not already exist.
     *
     * @param int    $userId
     * @param string $activityType
     */
    public static function logActivity($userId, $activityType)
    {
        try {
            $existingActivity = self::where('user_id', $userId)
                ->where('activity_type', $activityType)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if (!$existingActivity) {
                return self::create([
                    'user_id'       => $userId,
                    'activity_type' => $activityType,
                ]);
            }
        } catch (\Exception $exception) {
            return false;
        }
    }
}
