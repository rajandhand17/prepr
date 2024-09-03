<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TrophyAwards extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'trophy_awards';
    protected $guarded = [];
    protected $fillable = [
        'name',
        'issue_trophy_date',
        'expiration_date',
        'trophy_code_id',
        'no_of_times_issued',
        'status',
        'description',
        'image',
        'user_id',
        'points_gained',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return 'U';
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getImageAttribute($value): string
    {
        $path = Storage::cloud()->url($value);
        if ($path === env('AWS_URL')) {
            return '';
        }

        return $path;
    }

    // get userData name for issue to
    /***
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select(['id', 'email', 'name']);
    }
    // get userData name for issue to

    // get Badge data
    /***
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function badgeData()
    {
        return $this->hasOne(BadgeDetail::class, 'award_id', 'id')->where('award_type', 'trophy');
    }
    // get Badge data
}
