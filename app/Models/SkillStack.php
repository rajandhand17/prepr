<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class SkillStack extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'skill_stacks';

    protected $fillable = [
        'title',
        'fr_CA_title',
        'skills',
        'description',
        'fr_CA_description',
    ];
    protected $casts = [
        'skills' => 'json',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getSkillStacks($language = 'en', $search = null)
    {
        try {
            if ($language == 'en') {
                $skill_stacks = static::select('id', 'title', 'skills', 'description');
            } else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language, 'title');

                //check whether the column exist in the db or not
                if (!$column_name || !Schema::hasColumn('skill_stacks', $column_name)) {
                    return false;
                }
                $description = LanguageColumnHelper::getLanguageColumnName($language, 'description');
                $skill_stacks = static::select('id', $column_name.' as title', 'skills', $description.' as description');
            }

            if ($search != null) {
                $column_name = isset($column_name) ? $column_name : 'title';
                $skill_stacks = $skill_stacks->where($column_name, 'like', '%'.$search.'%');
            }

            //take 20 results based from the table
            $skill_stacks = $skill_stacks->take(20)->get();

            //check if there are any results
            if (!$skill_stacks->isEmpty()) {
                return $skill_stacks;
            }

            return false;
        } catch(\Exception) {
            return false;
        }
    }
}
