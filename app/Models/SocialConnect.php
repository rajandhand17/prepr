<?php

namespace App\Models;

use App\Helpers\UtilityHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialConnect extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'social_connect';

    protected $fillable = [
        'title',
        'logo',
        'integration_status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getSocialConnect($search = null)
    {
        try {
            $social_connect_list = static::select('id', 'title', 'logo');

            //take 20 results based from the table
            $social_connect_list = $social_connect_list->where('integration_status', '1')->get();
            //check if there are any results
            if (!$social_connect_list->isEmpty()) {
                return $social_connect_list;
            }

            return false;
        } catch (\Exception $e) {
            UtilityHelper::logError($e);
            return false;
        }
    }
}
