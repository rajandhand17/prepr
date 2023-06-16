<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ProjectIndustry extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'project_industries';

    protected $fillable = [
        'name',
        'fr_CA_name',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getProjectIndustries($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_industry_list = static::select('id', 'name');
                //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'name');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('skills', $column_name)) {
                    return false;
                }
                $project_industry_list = static::select('id', $column_name.' as name');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'name';
                $project_industry_list = $project_industry_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_industry_list = $project_industry_list->take(20)->get();

            //check if there are any results
            if (!$project_industry_list->isEmpty()) {
                return $project_industry_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
