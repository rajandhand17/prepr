<?php

namespace App\Models;

use App\Helpers\UtilityHelper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserPersonal extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'user_personal_details';

    protected $fillable = [
        'user_id', 'about', 'gender', 'date_of_birth', 'age', 'purpose', 'user_type', 'recent_immigrant', 'indigenous_group', 'visible_minority', 'disability',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public static function create(User $user, $request)
    {
        try {
            DB::beginTransaction();
            $userpersonal = new UserPersonal();
            $userpersonal->user_id = $user->id;
            $userpersonal->user_type = config('constants.user_types.'.$request->user_type);
            $userpersonal->save();
            if ($userpersonal) {
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
