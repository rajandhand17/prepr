<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Validator;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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


    public function register($request)
    {  
        $validation_array = [
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
            'username' => 'required|string|max:20|regex:/^[A-Za-z0-9_-]*$/|unique:users',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'email' => 'required|string|email|max:50|unique:users',
            'user_type' => 'required',
            'status' => 'required|min:1',
        ];
        $validation = Validator::make($request->all(), $validation_array);
        if ($validation->fails()) {
            return response()->json(['status' => 'fail', 'message' => $validation->messages()->first()], 200);
        }
        try {
            $name=request('first_name')."-".request('last_name');
            $user = User::create([
                'first_name'=>request('first_name'),
                'last_name'=>request('last_name'),
                'name' => $name,
                'username' => request('username'),
                'email' => request('email'),  
                'password' => $request->password
            ]);

            $sting = str_random(30);
            $user->username = request('username');
            $user->remember_token = $sting;
            $user->verify_token = $sting;
            $user->mycode = request('username');
            $user->device_token = request('device_token');
            $user->device_platform = request('device_platform');
            $data['user'] = $user;
            $user->syncRoles(['user']);
            $user->save();
            $user_id = $user->id;
            
            UserPersonal::updateOrCreate([
                'user_id' => $user_id,
                'status' => request('status'),
                'user_type' => request('user_type'),
            ]);
        } catch (\Throwable $th) {
            return false;
        }
    }
}
