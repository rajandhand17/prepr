<?php

namespace App\Models;

use App\Helpers\UtilityHelper;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserSSOLogin extends Model
{
    use SoftDeletes;

    protected $table = 'user_sso_login';

    protected $fillable = [
        'user_id', 'sso_type', 'sub', 'access_token',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public static function create(User $user, $request)
    {
        try {
            DB::beginTransaction();
            $usersso = new UserSSOLogin();
            $usersso->user_id = $user->id;
            $usersso->sso_type = $request->sso_type;
            $usersso->sub = $request->sub;
            $usersso->access_token = $request->access_token;
            $usersso->save();
            if ($usersso) {
                DB::commit();

                return true;
            }
            DB::rollback();

            return false;
        } catch (Exception $e) {
            UtilityHelper::logError($e);
            DB::rollback();
        }
    }
}
