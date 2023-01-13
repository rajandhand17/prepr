<?php

namespace App\Models;

//use App\Http\Requests\RegisterDataRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\UserPoint;
use App\Models\AutoCreateTemplate;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Event;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'country_code',
        'verification',
        'name',
        'email',
        'password',
        'two_factor',
        'two_factor_otp',
        'is_login',
        'profile_image',
        'phone_number',
        'fr_request',
        'fr_accept',
        'point',
        'rank',
        'remember_token',
        'is_verify',
        'is_email_sent',
        'verify_token',
        'mycode',
        'isReferralOpen',
        'manage_alerts',
        'is_subscribe',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function syncRoles()
    {
        return $this->belongsToMany(Role::class);

    }

    public function hasRole($role)
    {
        return Role::where('role', $role)->get();
    }

    function generateRandomString($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
   public function register($data)
    {     
    }
}

