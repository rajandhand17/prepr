<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class SkillGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'skill_groups';
        
    protected $fillable = [
        'title',
        'fr_CA_title',
        'skill_stacks',
        'skills',
        'description',
        'fr_CA_description',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getSkillGroups($language='en',$search=null,$skill_stacks=null,$skills=null)
    {    
        try{
            if($language == 'en'){
               $skill_group=static::select("id","title","skill_stacks","skills","description");
            }
            else {
                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language,'title');
                
                //check whether the column exist in the db or not
                if(!$column_name || !Schema::hasColumn('skill_groups', $column_name)){
                    return false;
                }
                
                $skill_group = static::select('id', $column_name . ' as title',"skill_stacks","skills","description");
            }

             //Search skill name based on user input
             if($search!=null){
                $column_name = isset($column_name) ? $column_name : "title";
                $skill_group = $skill_group->where($column_name,"like",'%'.$search.'%');
            }

            //Search skill stacks based on used input
            if($skill_stacks!=null){
                $skill_group = $skill_group->where("skill_stacks","like",'%'.$skill_stacks.'%');
            }

            //Search skill based on used input
            if($skills!=null){
                $skill_group = $skill_group->where("skills","like",'%'.$skills.'%');
            }

            //take 20 results based from the table
            $skill_group = $skill_group->take(20)->get();

            //check if there are any results
             if(!$skill_group->isEmpty()){
                return $skill_group;
            }

            return false;
         }
        catch (\Exception $e){
            return false;
        }
    }

}
