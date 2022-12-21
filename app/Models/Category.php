<?php

namespace App\Models;

use App\Helpers\LanguageColumnHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'fr_CA_name',
        'components',
        'parent_id'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public function getCategories($language='en',$search=null,$component=null)
    {
        try{
            if($language == 'en'){
                $category_list = static::select('id','name','parent_id');
            }
            else {

                //get column name based on language
                $column_name = LanguageColumnHelper::getLanguageColumnName($language,'name');

                //check whether the column exist in the db or not
                if(!$column_name || !Schema::hasColumn('categories', $column_name)){
                    return false;
                }
                $category_list = Skill::select('id', $column_name . ' as name','parent_id');
            }

            //Search categories based on user input
            if($search!=null){
                $category_list = $category_list->where("name","like",'%'.$search.'%');
            }

            //get categories based on component
            if($component!=null){
                $category_list = $category_list->where($component,"like",'%'.$component.'%');
            }

            //take 20 results based from the table
            $category_list = $category_list->take(20)->get();

            //check if there are any results
            if(!$category_list->isEmpty()){
                return $category_list;
            }

            return false;
        }
        catch (\Exception $e){
            return false;
        }

    }
}
