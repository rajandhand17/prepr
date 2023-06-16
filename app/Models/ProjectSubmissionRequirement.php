<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ProjectSubmissionRequirement extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'project_submission_requirements';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getProjectSubmissionRequirements($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $project_submission_requirements = static::select('id', 'title', 'status');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');
                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('project_submission_requirements', $column_name)) {
                    return false;
                }
                $project_submission_requirements = static::select('id', $column_name.' as title', 'status');
            }
            //Search categories based on user input
            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $project_submission_requirements = $project_submission_requirements->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $project_submission_requirements = $project_submission_requirements->take(20)->get();

            //check if there are any results
            if (!$project_submission_requirements->isEmpty()) {
                return $project_submission_requirements;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
