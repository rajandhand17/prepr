<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Skill extends Model
{
    use HasFactory;

    use SoftDeletes;
    protected $fillable = ['skill','language','fr_CA_skill'];


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

    public function getAll($skillName=null)
    {   
        try{
            if($skillName==null){
                $Skill_list = static::get();
            }else{
                $Skill_list = static::where('skill','like','%'.$skillName.'%')->get();
            }
            
            if(!$Skill_list->isEmpty()){
                return $Skill_list;
            }
            return false;
        }
        catch (\Exception $e){
            return false;
        }

    }
 
}
