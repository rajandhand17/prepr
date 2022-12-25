<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Tag extends Model
{
    use HasFactory;
    
    use SoftDeletes;

    protected $table="tags";

    protected $fillable =[
         'name','fr_CA_name','tag_image','fr_CA_tag_image','components'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    //for fetch the records with filter and without filter also
    public function getTags($language='en',$search=null)
    {
        try{
            if($language == 'en'){
                $tag_list = static::select('id','name','tag_image','components');
                  //Search categories based on user input
            }
            else {
                
                 //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language,'name');
                
                //check whether the column exist in the db or not
                if(!$column_name || !Schema::hasColumn('tags', $column_name)){
                    return false;
                }
                //get image column name based on language
                $image_column=LanguageColumnHelper::getLanguageColumnName($language,'tag_image');

                if(!$image_column || !Schema::hasColumn('tags', $image_column)){
                    return false;
                }

                $tag_list = static::select('id', $column_name . ' as name',$image_column.' as tag_image');
            }

            //Search categories based on user input
            if($search!=null){
                $column_name = isset($column_name) ? $column_name : "name";
                $tag_list = $tag_list->where($column_name,"like",'%'.$search.'%');
            }

            //take 20 results based from the table
            $tag_list = $tag_list->take(20)->get();

            //check if there are any results
            if(!$tag_list->isEmpty()){
                return $tag_list;
            }

            return false;
              
        }catch(\Exception $e){
            return false;
        }
    }

}
