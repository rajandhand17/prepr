<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Rank extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'ranks';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'description',
        'fr_CA_description',
        'image',
        'category',
        'point',
        'no_of_use',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getRanks($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $rank = static::select('id', 'title', 'description', 'image', 'category', 'point', 'no_of_use', 'status');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('ranks', $column_name)) {
                    return false;
                }
                $description = LanguageColumnHelper::getLanguageColumnName($language, 'description');

                $rank = static::select('id', $column_name.' as title', $description.' as description', 'image', 'category', 'point', 'no_of_use', 'status');
            }
            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $rank = $rank->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $rank = $rank->take(20)->get();

            //check if there are any results
            if (!$rank->isEmpty()) {
                return $rank;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
