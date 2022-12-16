<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Console\Input\Input;

class Category extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $table = 'categories';
    protected $fillable = [
        'language',
        'parent_id',
        'name',
        'lft',
        'rgt',
        'depth',
        'components'
    ];
    protected $hidden = ['created_at', 'updated_at', 'parent_id', 'lft'];

    /***
     * @param $value
     * @return string
     */

    public function getDeletedAtAttribute($value)
    {
        if ($value === null) {
            return '';
        }
    }

    public function parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    public function getAll($categoryName=null)
    {   
        try{
            if($categoryName==null){
                $category_list = static::take(20)->get();
            }else{
                $category_list = static::where("name","like",'%'.$categoryName.'%')->take(20)->get();
            }
           
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
