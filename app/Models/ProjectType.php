<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ProjectType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'project_types';

    protected $fillable = [
        'title',
        'fr_CA_title',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getProjectTypes($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_type_list = static::select('id', 'title');
                //Search categories based on user input
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('project_types', $column_name)) {
                    return false;
                }
                $project_type_list = static::select('id', $column_name.' as title');
            }

            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_type_list = $project_type_list->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_type_list = $project_type_list->take(20)->get();

            //check if there are any results
            if (!$project_type_list->isEmpty()) {
                return $project_type_list;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
