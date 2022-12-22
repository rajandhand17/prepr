<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
 

class Skill extends Model
{
    use HasFactory;

    use SoftDeletes;
    
    protected $table = 'skills';
    
    protected $fillable = [
        'name',
        'fr_CA_name'
    ];

   
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    /***
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /***
     * @param $value
     * @return string 
     */
    public function getDeletedAtAttribute($value)
    {
        if ($value == null) {
            return "";
        }
    }

    public function getSkills($language='en',$search=null)
    {  
        try{
            if($language == 'en'){
                $skill_list = static::select('id','name');
            }
            else {

                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language,'name');

                //check whether the column exist in the db or not
                if(!$column_name || !Schema::hasColumn('skills', $column_name)){
                    return false;
                }
                $skill_list = static::select('id', $column_name . ' as name');
            }

            //Search categories based on user input
            if($search!=null){
                $column_name = isset($column_name) ? $column_name : "name";
                $skill_list = $skill_list->where($column_name,"like",'%'.$search.'%');
            }

            //take 20 results based from the table
            $skill_list = $skill_list->take(20)->get();

            //check if there are any results
            if(!$skill_list->isEmpty()){
                return $skill_list;
            }

            return false;
        }
        catch (\Exception $e){
            return false;
        }
    }
 
}
