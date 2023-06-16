<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class FlexibleExpireDateDuration extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'flexible_expire_date_durations';

    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getFlexibleDateDurations($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $flexible_date_duration = static::select('id', 'title');
                //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('flexible_expire_date_durations', $column_name)) {
                    return false;
                }
                $flexible_date_duration = static::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $flexible_date_duration = $flexible_date_duration->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $flexible_date_duration = $flexible_date_duration->take(20)->get();

            //check if there are any results
            if (!$flexible_date_duration->isEmpty()) {
                return $flexible_date_duration;
            }

            return false;
        } catch (\Exception) {
            return false;
        }
    }
}
