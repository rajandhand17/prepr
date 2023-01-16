<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class LabCondition extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table="lab_conditions";

    protected $fillable=[
        "title",
        "fr_CA_title"
    ];
    
    
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getLabConditions($language="en",$search=null)
    {
        
        try{
            if($language == 'en'){
                $lab_conditions = static::select('id','title');
            }else {
                 //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language,'title');

                //check whether the column exist in the db or not
                if(!$column_name || !Schema::hasColumn('lab_conditions', $column_name)){
                    return false;
                }
                $lab_conditions = static::select('id', $column_name . ' as title');
            }
 
            //Search categories based on user input
            if($search!=null){
                $column_name = isset($column_name) ? $column_name : "title";
                $lab_conditions = $lab_conditions->where($column_name,"like",'%'.$search.'%');
            }

            //take 20 results based from the table
            $lab_conditions = $lab_conditions->take(20)->get();

            //check if there are any results
            if(!$lab_conditions->isEmpty()){
                return $lab_conditions;
            }

            return false;
        }
        catch (\Exception $e){
            return false;
        }
    }
}
