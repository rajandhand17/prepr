<?php

namespace App\Models;


use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class ProjectStatus extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $table = 'project_status';
    
    protected $fillable = [
        'name',
        'fr_CA_name'
    ];

   
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    
    public function getProjectStatus($language='en',$search=null)
    {
        try{
            if($language == 'en'){
                $project_status_list = static::select('id','name');
                  //Search categories based on user input
            }
            else {
                 //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language,'name');

                //check whether the column exist in the db or not
                if(!$column_name || !Schema::hasColumn('project_status', $column_name)){
                    return false;
                }
                $project_status_list = static::select('id', $column_name . ' as name');
            }
 
            //Search categories based on user input
            if($search!=null){
                $column_name = isset($column_name) ? $column_name : "name";
                $project_status_list = $project_status_list->where($column_name,"like",'%'.$search.'%');
            }

            //take 20 results based from the table
            $project_status_list = $project_status_list->take(20)->get();

            //check if there are any results
            if(!$project_status_list->isEmpty()){
                return $project_status_list;
            }

            return false;
        }
        catch (\Exception $e){
            return false;
        }
    }
}
