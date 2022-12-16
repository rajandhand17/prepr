<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Tag extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = ['language', 'tag', 'challenge', 'tag_image', 'resource', 'lab', 'category'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @var array
     */
    protected $casts = [
        'language' => 'string',
        'tag' => 'string',
        'challenge' => 'string',
        'tag_image' => 'string',
        'lab' => 'string',
        'resource' => 'string',
        'category' => 'string',
    ];

    /**
     * @param $value
     * @return string
     */
    public function getAll($tagName=null)
    {   
        try{
            if($tagName==null){
                $tag_list = static::get();
            }else{
                $tag_list = static::where("tag","like",'%'.$tagName.'%')->get();
            }
            
            if(!$tag_list->isEmpty()){
                return $tag_list;
            }
            return false;
        }
        catch (\Exception $e){
            return false;
        }

    }
}
