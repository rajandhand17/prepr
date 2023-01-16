<?php

namespace App\Models;

//use App\Http\Requests\RegisterDataRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Models\UserPersonal;


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
       try{
        DB::beginTransaction();
        $name=$data['first_name']."".$data['last_name'];
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],      
            'name' => $name,  
            'password' => $data['password'],
        ]);
        $sting = self::generateRandomString(30);
        $user->username = $data['username'];
        $user->first_name=$data['first_name'];
        $user->last_name=$data['last_name'];
        $user->remember_token = $sting;
        $user->verify_token = $sting;
        $user->mycode = $data['username'];
        $user->save();
        $user_id = $user->id;
        UserPersonal::updateOrCreate([
            'user_id' => $user_id,
            'status' => $data['status'],
            'user_type' => $data['user_type'],
        ]); 
        DB::commit();
        return response()->json(['status' => 'success', 'message' => 'You have registered successfully.', 'data' => $data], 200);
       }catch (\Exception $e){
        DB::rollback();
        
        return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
       }        
    }
}

