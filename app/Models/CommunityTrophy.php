<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommunityTrophy extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'community_trophy';
    protected $guarded = [];

    protected $hidden = [
        'deleted_at', 'category', 'no_of_use',
    ];

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return 'U';
    }

    /**
     * @var array
     */
    protected $casts = [
        'fb_point'                     => 'int',
        'google_point'                 => 'int',
        'linked_point'                 => 'int',
        'login_point'                  => 'int',
        'create_project_point'         => 'int',
        'join_lab_point'               => 'int',
        'submit_project_point'         => 'int',
        'success_submit_project_point' => 'int',
        'add_member_point'             => 'int',
        'vote_project_point'           => 'int',
        'reply_chat_point'             => 'int',
        'create_chat_point'            => 'int',
        'description'                  => 'string',
        'badge_type'                   => 'string',
        'issuer'                       => 'string',
        'criteria'                     => 'string',
    ];

    // public $appends = ['image'];

    protected function castAttribute($key, $value)
    {
        if ($value !== null) {
            return parent::castAttribute($key, $value);
        }

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int) 0;
            case 'real':
            case 'float':
            case 'double':
                return (float) 0;
            case 'string':
                return '';
            case 'bool':
            case 'boolean':
                return false;
            case 'object':
            case 'array':
            case 'json':
                return [];
            case 'collection':
                return new BaseCollection();
            case 'date':
                return $this->asDate('0000-00-00');
            case 'datetime':
                return $this->asDateTime('0000-00-00');
            case 'timestamp':
                return $this->asTimestamp('0000-00-00');
            default:
                return $value;
        }
    }

    /***
     * @return BelongsTo
     */
    public function wonTrophy()
    {
        $userId = Auth::user()->id;

        return $this->belongsTo(UserTrophy::class, 'id', 'trophy_id')->where('user_id', $userId);
    }

    /***
     * @return BelongsTo
     */
    public function wonTrophyData()
    {
        return $this->belongsTo(UserTrophy::class, 'id', 'trophy_id');
    }

    /***
     * @return BelongsTo
     */
    public function userWonTrophy()
    {
        $userId = Auth::user()->id;

        return $this->belongsTo(UserTrophy::class, 'id', 'trophy_id')->where('user_id', $userId)->where('status', '1');
    }

    /***
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userTrophyData()
    {
        return $this->hasMany(UserTrophy::class, 'trophy_id', 'id')->where(['status'=>'1', 'trophy_type'=>'communityTrophy']);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getImageAttribute($value)
    {
        $path = Storage::cloud()->url($value);
        if ($path === env('AWS_URL')) {
            return '';
        }

        return $path;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getDescriptionAttribute($value)
    {
        if ($value === null) {
            return '';
        } else {
            return $value;
        }
    }

    // get Badge data
    /***
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function badgeData()
    {
        return $this->hasOne(BadgeDetail::class, 'award_id', 'id')->where('award_type', 'community');
    }
    // get Badge data
}
